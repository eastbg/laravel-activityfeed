
<div class="af_feed_container">
@foreach($feed as $item)
    <div class="af_feed_item">
        {{$item->AfRule->AfTemplate->notification_template}}
    </div>
    @endforeach
</div>