<div class="dataobject-gallery row">
    <% loop OtherImages %>
    <div class="col-md-3 col-xs-6">
        <div class="thumbnail text-center">
            <a href="$Image.URL" data-lightbox="dataobject-gallery" data-title="{$Title} <p>{$Content}</p>">
                <img src="$Image.Square(150).URL" alt="{$Title}" class="img-responsive" />
                <div class="caption">
                    <h4>$Title</h4>
                    <p>$Description.LimitCharacters(110)</p>
                </div>
            </a>
        </div>
    </div>
    <% end_loop %>
</div>