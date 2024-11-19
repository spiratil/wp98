$.ajax({
  type: "POST",
  url: ajax_object.ajax_url,
  data: { action : 'inline_search', input: $("#searchinput").val() },
  success: function(result) {
      $("#SearchResults").html(result);
  }
});