<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageTranslation extends Model
{
  protected $fillable = ['page_id', 'title', 'content', 'lang'];

  public function page(){
    return $this->belongsTo(Page::class);
  }
}
