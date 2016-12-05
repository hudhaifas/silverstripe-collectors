<% if $Results %>
    <% include Collection_List %>
<% else_if $Item %>
    <% include Collection_Single %>
<% else %>
    
<% end_if %>