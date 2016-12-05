<% include Menu_Side %>

<article class="col-md-8">

    <div class="row">
        $SearchBook
    </div>

    <% if $Query %>
    <div class="row">
        <%t Collectors.SEARCH_QUERY 'You searched for &quot;{value}&quot;' value=$Query %>
    </div>
    <% end_if %>

    <div class="row">
        <% if $Results %>
        <% loop $Results %>
        <div class="col-md-4">
            <a href="$Link">
                <div class="thumbnail text-center books-stamp">
                    <% if $FrontImage %>
                    <img src="$FrontImage.PaddedImage(207,207).URL" alt="image" class="img-responsive zoom-img" />
                    <% else %>
                    <img alt="" class="img-responsive" src= "collectors/images/default-stamp.png" />

                    <div class="caption" style="">
                        <h4>$Title.LimitCharacters(110)</h4>
                    </div>
                    <% end_if %>
                </div>

                <div>
                    <% if $Denomination != '0.00' %><h5>$Denomination $Currency</h5><% end_if %>
                    <h6>$Country</h6>
                    <% if $TheDate %><p><%t Collectors.YEAR 'Year' %>: $TheDate</p><% end_if %>
                    <p>$Subject.LimitCharacters(27)</p>
                    <% if $Condition %><p><%t Collectors.CONDITION 'Condition' %>: $Condition</p><% end_if %>
                </div>		
            </a>
        </div>
        <% end_loop %>
        <% else %>
        <p><%t Collectors.SEARCH_NO_RESULTS 'Sorry, your search query did not return any results.' %></p>
        <% end_if %>
    </div>

    <div class="row">
        <% with $Results %>
        <% include Paginate %>
        <% end_with %>
    </div>
</article>