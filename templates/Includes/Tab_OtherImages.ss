<% loop OtherImages %>
<div class="row">
    <div class="col-md-3">
        <img src="$Image.PaddedImage(300,300).Watermark.URL" data-origin="$Image.Watermark.URL" class="img-responsive" />
    </div>

    <div class="col-md-9">
        <strong>$Title</strong>
        <p>$Description</p>
    </div>
</div>
<% end_loop %>