<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <title>Low level rest example</title>
  <script src="min.js"></script>
</head>
<body>
  <button id="get">GET request</button>
  <button id="post">POST request</button>
  <button id="put">PUT request</button>
  <button id="delete">DELETE request</button>

  <script>
  $(document).ready(function() {
    var requestTypes = ["get", "post", "put", "delete"];

    $.each(requestTypes, function( index, value ) {
      $('#' + value).on('click', function() {
        request = triggerRequest(value);
      });
    });

    function triggerRequest(requestType) {
      if ($.inArray(requestType, requestTypes) !== -1) {
        var data = {firstname: "John", lastname: "doe"};
        return $.ajax({
          method: requestType,
          url: "method.php",
          cache: false,
          async: true,
          data: data
        }).done(function(msg) {
          alert("request methode = " + msg);
        }).fail(function( jqXHR, textStatus ) {
          console.error( "Request failed: ");
          console.error( textStatus );
        });
      }
    }
  });
  </script>
</body>
</html>
