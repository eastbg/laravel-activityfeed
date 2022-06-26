<style>
    .af_email_digest {

    }

    .af_email_digest_item {

    }
</style>

<div class="af_email_digest">
    @foreach($items as $item)
        <div class="af_email_digest_item">
            {!! $item !!}}
        </div>
    @endforeach
</div>