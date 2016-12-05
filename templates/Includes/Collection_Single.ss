<% with Collectable %>
<div class="col-md-9">
    <div class="row">
        <div class="col-lg-5 col-md-6 col-xs-12">
            <% include Images_Coin %>
        </div>

        <div class="col-lg-7 col-md-6 col-xs-12">
            <h1><a href="$Link">$Denomination $Currency</a></h1>

            <h4>$Country $TheDate</h4>

            <% if $Subject %><p class="information"><%t Librarian.SUBJECT 'Subject' %>: $Subject</p><% end_if %>
            <% if $Quantity %><p class="information"><%t Librarian.QUANTITY 'Quantity' %>: $Quantity</p><% end_if %>

            <!-- Collections -->
            <% if Collections %>
            <div>
                <h5><%t Librarian.COLLECTIONS 'Collections' %></h5>
                <span class="information">
                    <% loop Collections %>
                    <a href="$Link">$Title</a><% if not Last %><%t Librarian.COMMA ',' %> <% end_if %>
                    <% end_loop %>
                </span>
            </div>
            <% end_if %>
        </div>

    </div>

    <div class="row">
        <div>
            <% if $Description %>
            <h4><%t Librarian.DESCRIPTION "Description" %></h4>

            <div class="resp-tabs-container">
                $Description
            </div>
            <% end_if %>
        </div>
    </div>
</div>

<div class="col-md-3">
    <% if $Related %>
    <h3 class="m_1"><%t Librarian.ALSO_READ "Also Read" %></h3>

    <% loop $Related.Limit(4) %>
    <% include Related_Volume %>
    <% end_loop %>
    <% end_if %>
</div>
<% end_with %>