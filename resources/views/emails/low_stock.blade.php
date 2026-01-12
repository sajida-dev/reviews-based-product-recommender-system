@component('mail::message')

    <img src="{{ asset('img/e-com-logo.png') }}" alt="Logo" style="width:120px; margin-bottom:20px;">

    # Low Stock Alert

    The following product is running low on stock:

    @component('mail::panel')
        **Product:** {{ $productName }}
        **Current Stock:** {{ $productStock }}
        <img src="{{ $productImage }}" alt="{{ $productName }}" style="width:100px; margin-top:10px;">
    @endcomponent

    @component('mail::button', ['url' => $productUrl])
        View Product
    @endcomponent

    Thanks,<br>
    **Your Store Team**
@endcomponent
