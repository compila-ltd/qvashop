@php

require  base_path('/vendor/autoload.php');

MercadoPago\SDK::setAccessToken(config('mercadopago.access'));

$preference = new MercadoPago\Preference();

  $payer = new MercadoPago\Payer();
  $payer->name = $first_name;
  $payer->email = $email;
  $payer->phone = array(
    "area_code" => "",
    "number" => $phone
  );

// Crea un ítem en la preferencia

$item = new MercadoPago\Item();
$item->title = $billname;
$item->quantity = 1;
$item->unit_price = $amount;
$preference->payer = $payer;
$preference->items = array($item);

$preference->back_urls = array(
    "success" => $success_url,
    "failure" => $fail_url,
    "pending" => $fail_url
);

$preference->save();

@endphp

<html>
  <head>
    <title>Mercadopago Payment</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
    .loader {
      border: 16px solid #f3f3f3;
      border-radius: 50%;
      border-top: 16px solid #3498db;
      width: 120px;
      height: 120px;
      -webkit-animation: spin 2s linear infinite; /* Safari */
      animation: spin 2s linear infinite;
      margin: auto;
    }

    /* Safari */
    @-webkit-keyframes spin {
      0% { -webkit-transform: rotate(0deg); }
      100% { -webkit-transform: rotate(360deg); }
    }

    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
    </style>
    <script src="https://sdk.mercadopago.com/js/v2"></script>
  </head>
  <body>
    <div class="cho-container"style="display: none;"></div>
    <br>
    <br>
    <script>
  // Agrega credenciales de SDK
  const mp = new MercadoPago('{{ env("MERCADOPAGO_KEY") }}', {
    locale: "{{ env('MERCADOPAGO_CURRENCY') }}",
    advancedFraudPrevention:true,
  });

  // Inicializa el checkout
  const checkout = mp.checkout({
    
    preference: {
      id: '{{ $preference->id }}',
    },
    autoOpen: true,
    render: {
      container: ".cho-container", // Indica el nombre de la clase donde se mostrará el botón de pago
      label: "Pagar", // Cambia el texto del botón de pago (opcional)
    },
  });

</script>
  </body>
</html>
