<div class="dataobject-gallery row">
    <div class="col-md-3 col-xs-6">
        <div class="thumbnail text-center">
            <a href="$BackImage.Watermark.URL" data-lightbox="dataobject-gallery" data-title="{$Title} <p>{$Content}</p>">
                <img src="$BackImage.SetHeight(300).SetWidth(300).Watermark.URL" alt="{$Title}" class="img-responsive" />
                <div class="caption">
                    <h4>$Title</h4>
                    <p>$Description.LimitCharacters(110)</p>
                </div>
            </a>
        </div>
    </div>
</div>