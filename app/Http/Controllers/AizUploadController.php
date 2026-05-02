<?php

namespace App\Http\Controllers;

use App\Models\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class AizUploadController extends Controller
{
    public function index(Request $request)
    {
        $all_uploads = (auth()->user()->user_type == 'seller') ? Upload::where('user_id', auth()->user()->id) : Upload::query();
        $search = null;
        $sort_by = null;

        if ($request->search != null) {
            $search = $request->search;
            $all_uploads->where('file_original_name', 'like', '%' . $request->search . '%');
        }

        $sort_by = $request->sort;
        switch ($request->sort) {
            case 'newest':
                $all_uploads->orderBy('created_at', 'desc');
                break;
            case 'oldest':
                $all_uploads->orderBy('created_at', 'asc');
                break;
            case 'smallest':
                $all_uploads->orderBy('file_size', 'asc');
                break;
            case 'largest':
                $all_uploads->orderBy('file_size', 'desc');
                break;
            default:
                $all_uploads->orderBy('created_at', 'desc');
                break;
        }

        $all_uploads = $all_uploads->paginate(60)->appends(request()->query());

        return (auth()->user()->user_type == 'seller')
            ? view('seller.uploads.index', compact('all_uploads', 'search', 'sort_by'))
            : view('backend.uploaded_files.index', compact('all_uploads', 'search', 'sort_by'));
    }

    // Create a n upload File
    public function create()
    {
        return (auth()->user()->user_type == 'seller')
            ? view('seller.uploads.create')
            : view('backend.uploaded_files.create');
    }

    // Show Uploader Modal
    public function show_uploader(Request $request)
    {
        return view('uploader.aiz-uploader');
    }

    // Allowed Uploads
    public function upload(Request $request)
    {
        $type = array(
            "jpg" => "image",
            "jpeg" => "image",
            "png" => "image",
            "svg" => "image",
            "webp" => "image",
            "gif" => "image",
            "mp4" => "video",
            "mpg" => "video",
            "mpeg" => "video",
            "webm" => "video",
            "ogg" => "video",
            "avi" => "video",
            "mov" => "video",
            "flv" => "video",
            "swf" => "video",
            "mkv" => "video",
            "wmv" => "video",
            "wma" => "audio",
            "aac" => "audio",
            "wav" => "audio",
            "mp3" => "audio",
            "zip" => "archive",
            "rar" => "archive",
            "7z" => "archive",
            "doc" => "document",
            "txt" => "document",
            "docx" => "document",
            "pdf" => "document",
            "csv" => "document",
            "xml" => "document",
            "ods" => "document",
            "xlr" => "document",
            "xls" => "document",
            "xlsx" => "document"
        );

        if (!$request->hasFile('aiz_file')) {
            return response()->json(['error' => 'No file uploaded'], 400);
        }

        $file = $request->file('aiz_file');
        $extension = strtolower($file->getClientOriginalExtension());

        if (!isset($type[$extension])) {
            return response()->json(['error' => 'File type not allowed'], 400);
        }

        try {
            $upload = new Upload;
            
            $upload->file_original_name = null;
            $arr = explode('.', $file->getClientOriginalName());
            for ($i = 0; $i < count($arr) - 1; $i++) {
                if ($i == 0) {
                    $upload->file_original_name .= $arr[$i];
                } else {
                    $upload->file_original_name .= "." . $arr[$i];
                }
            }

            $path = $file->store('uploads/all');
            $size = $file->getSize();

            // Get the MIME type of the file
            $file_mime = $file->getMimeType();

            if ($type[$extension] == 'image' && get_setting('disable_image_optimization') != 1) {
                try {
                    $manager = new ImageManager(new Driver());
                    $img = $manager->read($file->getRealPath());
                    $height = $img->height();
                    $width = $img->width();
                    
                    if ($width > $height && $width > 1500) {
                        $img->scale(width: 1500);
                    } elseif ($height > 1500) {
                        $img->scale(height: 800);
                    }
                    
                    $img->save(base_path('public/') . $path);
                    clearstatcache();
                    $size = filesize(base_path('public/') . $path);
                } catch (\Exception $e) {
                    \Log::warning('Image optimization failed: ' . $e->getMessage());
                }
            }

            if (env('FILESYSTEM_DRIVER') == 's3') {
                try {
                    Storage::disk('s3')->put(
                        $path,
                        file_get_contents(base_path('public/') . $path),
                        [
                            'visibility' => 'public',
                            'ContentType' =>  $extension == 'svg' ? 'image/svg+xml' : $file_mime,
                            'CacheControl' => 'max-age=31536000'
                        ]
                    );
                    
                    // Only unlink if file exists and upload was successful
                    if ($arr[0] != 'updates' && file_exists(base_path('public/') . $path)) {
                        unlink(base_path('public/') . $path);
                    }
                } catch (\Exception $e) {
                    \Log::error('S3 Upload failed: ' . $e->getMessage());
                    return response()->json(['error' => 'S3 upload failed: ' . $e->getMessage()], 500);
                }
            }

            $upload->extension = $extension;
            $upload->file_name = $path;
            $upload->user_id = Auth::user()->id;
            $upload->type = $type[$upload->extension];
            $upload->file_size = $size;
            
            if (!$upload->save()) {
                return response()->json(['error' => 'Failed to save upload record'], 500);
            }

            return response()->json([
                'success' => true,
                'file_id' => $upload->id,
                'file_name' => $upload->file_name,
                'original_name' => $upload->file_original_name,
                'file_size' => $upload->file_size
            ]);

        } catch (\Exception $e) {
            \Log::error('Upload failed: ' . $e->getMessage());
            return response()->json(['error' => 'Upload failed: ' . $e->getMessage()], 500);
        }
    }

    // Get uploaded File
    public function get_uploaded_files(Request $request)
    {
        $uploads = Upload::where('user_id', Auth::user()->id);
        if ($request->search != null) {
            $uploads->where('file_original_name', 'like', '%' . $request->search . '%');
        }
        if ($request->sort != null) {
            switch ($request->sort) {
                case 'newest':
                    $uploads->orderBy('created_at', 'desc');
                    break;
                case 'oldest':
                    $uploads->orderBy('created_at', 'asc');
                    break;
                case 'smallest':
                    $uploads->orderBy('file_size', 'asc');
                    break;
                case 'largest':
                    $uploads->orderBy('file_size', 'desc');
                    break;
                default:
                    $uploads->orderBy('created_at', 'desc');
                    break;
            }
        }
        return $uploads->paginate(60)->appends(request()->query());
    }

    // Destroy File
    public function destroy(Request $request, $id)
    {
        $upload = Upload::findOrFail($id);

        if (auth()->user()->user_type == 'seller' && $upload->user_id != auth()->user()->id)
            return back()->with('error', translate("You don't have permission for deleting this!"));

        try {
            if (env('FILESYSTEM_DRIVER') == 's3') {
                Storage::disk('s3')->delete($upload->file_name);
                if (file_exists(public_path() . '/' . $upload->file_name)) {
                    unlink(public_path() . '/' . $upload->file_name);
                }
            } else {
                unlink(public_path() . '/' . $upload->file_name);
            }
            $upload->delete();
        } catch (\Exception $e) {
            $upload->delete();
        }

        return back()->with('success', translate('File deleted successfully'));
    }

    // Preview Files
    public function get_preview_files(Request $request)
    {
        $ids = explode(',', $request->ids);
        $files = Upload::whereIn('id', $ids)->get();
        $new_file_array = [];
        foreach ($files as $file) {
            $file['file_name'] = my_asset($file->file_name);
            if ($file->external_link) {
                $file['file_name'] = $file->external_link;
            }
            $new_file_array[] = $file;
        }
        // dd($new_file_array);
        return $new_file_array;
    }

    //Download project attachment
    public function attachment_download($id)
    {
        $project_attachment = Upload::find($id);
        
        try {
            $file_path = public_path($project_attachment->file_name);
            return Response::download($file_path);
        } catch (\Exception $e) {
            return back()->with('error', translate('File does not exist!'));
        }
    }

    //Download project attachment
    public function file_info(Request $request)
    {
        $file = Upload::findOrFail($request['id']);

        return (auth()->user()->user_type == 'seller')
            ? view('seller.uploads.info', compact('file'))
            : view('backend.uploaded_files.info', compact('file'));
    }
}
