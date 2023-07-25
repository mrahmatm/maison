<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
	<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <link rel="shortcut icon" href="#">

</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col">
                <br><h1>GET Request Test</h1><br>
                <label for="reqService">Service: </label>
                <input class="form-control" ="text" id="reqService">
                <label for="reqBody">Body (multiple parameters set apart with //): </label>
                <textarea class="form-control" type="text" id="reqBody" rows="10"></textarea>
            </div>
            <div class="col">
                <br><h1>Response</h1><br>
                <label for="response">Response body:</label><br>
                <span id="response"></span>
            </div>
        </div>
        <br><center><button class="btn btn-outline-success" onclick="getRequest()">GO</button></center>
    </div>

    <script>
        function getRequest(){
            var xmlhttp = new XMLHttpRequest();

            xmlhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("response").innerHTML = this.responseText;
                }
            };

            var string = document.getElementById("reqBody").value;
            var array = string.split("//");
            console.log(string)
            var tasks = "";
            array.forEach(function(element) {
                var current, key, data;
                current = element.split("=");
                key = current[0];
                data = current[1];
                data = encodeURIComponent(data);
                tasks += key+"="+data+"&";
                //console.log(current[1]);
            });
            console.log(tasks)
            var method = "method=" + encodeURIComponent(document.getElementById("reqService").value);
            //var tasks = encodeURIComponent(document.getElementById("reqBody").value);
            var url = "mobile request.php?" + method + "&" + tasks;
            xmlhttp.open("GET", url, true);
            xmlhttp.send();
            //saveInput();
        }

        document.addEventListener("keyup", function(event) {
            // Check if the pressed key is Enter (key code 13)
            if (event.keyCode === 13) {
                getRequest();
            }
        });

        document.getElementById("reqBody").addEventListener("keyup", function(event) {
            // Check if the pressed key is Enter (key code 13)
            if (event.keyCode === 13) {
                getRequest();
            }
        });


    </script>
</body>
</html>

