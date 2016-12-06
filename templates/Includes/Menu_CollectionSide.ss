<div class="col-md-4">
    <% if CollectionsList %>
    <h5 class="side-menu"><%t Collectors.COLLECTIONS "Collections" %></h5>
    <ul class="book-categories">
        <% loop CollectionsList.Limit(8) %>
        <li class="cat-item"><a href="$Link">$Title</a> <span class="count">($Collectables.Count)</span></li>
        <% end_loop %>
    </ul>
    <% end_if %>
</div>
