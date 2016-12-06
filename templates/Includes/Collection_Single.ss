<% with Item %>
<div class="col-md-9">
    <div class="row">
        <div class="col-lg-5 col-md-6 col-xs-12">
            <% include Images_Collectable %>
        </div>

        <div class="col-lg-7 col-md-6 col-xs-12">
            <% if $Denomination != '0.00' %><h1>$Denomination $Currency</h1><% end_if %>

            <h4>$Country $TheDate</h4>

            <% if $Subject %><p class="information"><%t Collectors.SUBJECT 'Subject' %>: $Subject</p><% end_if %>
            <% if $Quantity %><p class="information"><%t Collectors.QUANTITY 'Quantity' %>: $Quantity</p><% end_if %>

            <!-- Collections -->
            <% if $Description %>
            <div>
                <h5><%t Collectors.DESCRIPTION 'Description' %></h5>
                <span class="information">
                    $Description
                </span>
            </div>
            <% end_if %>

            <!-- Collections -->
            <% if Collections %>
            <div>
                <h5><%t Collectors.COLLECTIONS 'Collections' %></h5>
                <span class="information">
                    <% loop Collections %>
                    <a href="$Link">$Title</a><% if not Last %><%t Collectors.COMMA ',' %> <% end_if %>
                    <% end_loop %>
                </span>
            </div>
            <% end_if %>
        </div>

    </div>
</div>

<div class="col-md-3">
    <% if $Related %>
    <h3 class="m_1"><%t Collectors.ALSO_READ "Also Read" %></h3>

    <% loop $Related.Limit(4) %>
    <% include Related_Volume %>
    <% end_loop %>
    <% end_if %>
</div>
<% end_with %>