
<!DOCTYPE html>
<html>
<head>
    <title></title>
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, user-scalable=no"/>
    <link rel="stylesheet" href="{{ static_asset('assets/css/vendors.css') }}">
    <link rel="stylesheet" href="{{ static_asset('assets/css/aiz-core.css') }}">
    <link rel="stylesheet" href="{{ static_asset('assets/css/custom-style.css') }}">
</head>
<body>
    <section class="py-4 mb-4 bg-light">
        <div class="container text-center">
          <button id="bKash_button" class="d-none">Pay With bKash</button>            
        </div>
    </section>

    <!-- SCRIPTS -->
    <script src="{{ static_asset('assets/js/vendors.js') }}"></script>

    @if (get_setting('bkash_sandbox') == 1)
        <script src="https://scripts.sandbox.bka.sh/versions/1.2.0-beta/checkout/bKash-checkout-sandbox.js"></script>
    @else
        <script src="https://scripts.pay.bka.sh/versions/1.2.0-beta/checkout/bKash-checkout.js"></script>
    @endif

    <script type="text/javascript">        

        $(document).ready(function(){
            $('#bKash_button').trigger('click');
        });

        var paymentID = '';
        bKash.init({
        paymentMode: 'checkout', //fixed value ‘checkout’
        //paymentRequest format: {amount: AMOUNT, intent: INTENT}
        //intent options
        //1) ‘sale’ – immediate transaction (2 API calls)
        //2) ‘authorization’ – deferred transaction (3 API calls)
        paymentRequest: {
            amount: '{{ $amount }}', //max two decimal points allowed
            intent: 'sale'
        },
        createRequest: function(request) { //request object is basically the paymentRequest object, automatically pushed by the script in createRequest method
        $.ajax({
          url: '{{ route('api.bkash.checkout',['token'=>$token, 'amount'=>$amount]) }}',
          type: 'POST',
          contentType: 'application/json',
          success: function(data) {
            console.log('checkout  s');
            console.log(data);
            console.log('checkout  en');
            data = JSON.parse(data);
            if (data && data.paymentID != null) {
                paymentID = data.paymentID;
                bKash.create().onSuccess(data); //pass the whole response data in bKash.create().onSucess() method as a parameter
            } else {
                
                alert(data.errorMessage);
                bKash.create().onError();
            }
          },
          error: function() {
            bKash.create().onError();
          }
        });
        },
        executeRequestOnAuthorization: function() {
        $.ajax({
          url: '{{ route('api.bkash.execute', $token) }}',
          type: 'POST',
          contentType: 'application/json',
          data: JSON.stringify({
                        "paymentID": paymentID
                    }),
          success: function(data) {
            console.log('execute  s');
            console.log(data);
            console.log('execute  en');
            var result = JSON.parse(data);
            if (result && result.paymentID != null) {
                window.location.href = "{{ route('api.bkash.success') }}?payment_details="+data; //Merchant’s success page
            } else {
                alert(result.errorMessage);
                bKash.execute().onError();
            }
          },
          error: function() {
            bKash.execute().onError();
          }
        });
        }
        });
    </script>
</body>
</html>
