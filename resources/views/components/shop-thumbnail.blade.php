<div>
    @if(empty($shop->filename))
        <img src=" {{ asset('images/no_image.jpg') }}">
    @else
        <img src=" {{ asset('strage/shops/' . $shop->filename)}}">
    @endif
</div>