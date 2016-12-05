<div class="thumbnail text-center volume-default">
    <% if $Image %>
        <img class="img-responsive" src="$Image.PaddedImage(280, 410).URL" />
    <% else %>
        <img class="img-responsive" src= "librarian/images/book-cover.jpg" />

        <div class="caption" style="">
            <h4>$Title.LimitCharacters(110)</h4>
        </div>
    <% end_if %>
</div>