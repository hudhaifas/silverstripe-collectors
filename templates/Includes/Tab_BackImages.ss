<div class="row dataobject-grid dataobject-xs">
    <div class="col-sm-3 col-6 dataobject-item dataobject-item-brief">
        <div style="height: auto;">
            <a href="$Image.URL" data-lightbox="dataobject-gallery" data-title="{$Title} <p>{$Content}</p>">
                <div class="card text-center col-sm-12 dataobject-image">
                    <img src="$Image.Square(120).URL" loading="lazy" alt="{$Title}" class="img-responsive" />
                </div>

                <div class="content col-sm-12 ellipsis">
                    <p class="title">
                        $Title
                    </p>
                </div>		
            </a>
        </div>
    </div>
</div>
