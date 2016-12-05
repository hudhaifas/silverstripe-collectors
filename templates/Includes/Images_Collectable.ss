<div class="thumbnail text-center volume-default">
    <% if $FrontImage %>
        <img class="img-responsive" src="$FrontImage.PaddedImage(280, 280).URL" />
    <% else %>
        <img class="img-responsive" src= "collectors/images/default-stamp.png" />

        <div class="caption" style="">
            <h4>$Title.LimitCharacters(110)</h4>
        </div>
    <% end_if %>
</div>