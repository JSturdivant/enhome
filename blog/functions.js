


// FORCE HTTPS
/*if (location.host !== 'localhost') {
    if (location.protocol != 'https:')
    {
        location.href = 'https:' + window.location.href.substring(window.location.protocol.length);
    }
}*/

// LOAD jQuery
    //function loadJquery(){
        /*var newElement = document.createElement('script');
        var newAttribute = document.createAttribute('src');
        newAttribute.value = 'https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js';
        newElement.setAttributeNode(newAttribute);
        document.getElementsByTagName('head')[0].appendChild(newElement);*/
    //}
    //loadJquery();
//GLOBAL FUNCTIONS
    
    var logging = true; // TOGGLE TO TURN LOGGING ON AND OFF
    
    var userInfo = {
        "token": null,
        "userId": null,
        "userName": null,
        "firstName": null,
        "lastName": null,
        "email": null,
    };

    function log(message){
        if (logging){
            console.log(message);
        }
    }

    function getPathname(){
        var path = window.location.pathname;
        //console.log(window.location.pathname);
        var backupCount = ((path.match(/\//g) || []).length);
        
        if(path.search("/enhome/") >= 0){
            backupCount = backupCount-2;
        } else {
            backupCount = backupCount-1;
        }
        var returnPath = "";
        for(i=0; i < backupCount; i++){
            returnPath += "../";
        }
        //return "https://labs.tssands.com/";
        return returnPath;
        
    }
    loginCheck();
    //BUILD STANDARD HEADER
    function buildEhHeader(pageTitle){
        loginCheck();
        log(userInfo);
        var filePath = getPathname();
        var ehHeader = window.document.getElementById('enhomeHeader');
        var username = getCookie('username');
        
        var menuItems =[
            {'title': 'EnHome', 'html': '<li><a href="'+filePath+'index.html">EnHome</a></li>'},
            {'title': 'My Reports', 'html': '<li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">My Reports <span class="caret"></span></a>\n\
                <ul class="dropdown-menu">\n\
                    <li role="separator" class="divider"></li>\n\
                    <li><a href="'+filePath+'reporter/dispatchanalytics">Load Detail</a></li>\n\
                    <li role="separator" class="divider"></li><li class="dropdown-header">Other Reports</li>\n\
                    <li><a href="'+filePath+'reporter/explorer">Analytics Explorer</a></li>\n\
                </ul></li>'},
            
        ];

        document.title = "EnHome | " + pageTitle;
        
        var menuHtmlString = '<li><a href="'+filePath+'index.html">EnHome</a></li>';
            for(j=1; j < menuItems.length; j++){
                menuHtmlString += menuItems[j].html;
            }
            
        if(userData){
            var loginLogout = '<li><p style="color: #bbb; padding-top: 1.2em;">Logged in as ' + userInfo.userName + '</p></li>\n\
            <li><a href="'+filePath()+'users/logout.php" >Logout</a></li>';
        } else {
            var loginLogout = '<li><a href="'+filePath()+'users/login.php" >Login</a></li>';
        }
        ehHeader.innerHTML = '<nav class="navbar navbar-inverse" style="border-radius: 0px;">\n\
            <div class="container-fluid">\n\
                <div class="navbar-header">\n\
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">\n\
                        <span class="sr-only">Toggle navigation</span>\n\
                        <span class="icon-bar"></span><span class="icon-bar"></span>\n\
                        <span class="icon-bar"></span>\n\
                    </button>\n\
                    <a class="navbar-brand" href="#">\n\
                        <img src="" style="width: 1.5em;">\n\
                    </a>\n\
                </div>\n\
                <!-- Collect the nav links, forms, and other content for toggling -->\n\
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">\n\
                    <ul class="nav navbar-nav">\n\
                        ' + menuHtmlString + '\
                    </ul>\n\
                    <ul class="nav navbar-nav navbar-right">'+loginLogout+'\n\
                    </ul>\n\
                </div><!-- /.navbar-collapse -->\n\
            </div><!-- /.container-fluid -->\n\
        </nav><div class="col-sm-12"><h3>' + pageTitle + '</h3></div>';

    }
    
    function buildHtmlHeader(i = 0){
        var header = document.getElementsByTagName('head');
        //<!-- Global site tag (gtag.js) - Google Analytics -->


        var headElements = [
            { // SET VIEWPORT
                'type': 'META',
                'attributes': [
                    {'type': 'name', 'value': 'viewport'},
                    {'type': 'content', 'value': 'width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1'},
                ]
            },
            { // jQuery
                'type': 'SCRIPT',
                'attributes': [
                    {'type': 'src', 'value': 'https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js'},
                ]
            },
            { // DATA TABLES JS
                'type': 'SCRIPT',
                'attributes': [
                    {'type': 'src', 'value': 'https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js'},
                ]
            },
            { // DATA TABLES JS
                'type': 'SCRIPT',
                'attributes': [
                    {'type': 'src', 'value': 'https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap.min.js'},
                ]
            },
            { // DATA TABLES CSS
                'type': 'LINK',
                'attributes': [
                    {'type': 'href', 'value': 'https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap.min.css'},
                    {'type': 'rel', 'value': 'stylesheet'},
                ]
            },
            { // MOMENT JS
                'type': 'SCRIPT',
                'attributes': [
                    {'type': 'src', 'value': 'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.13.0/moment.min.js'},
                ]
            },
            { 
                'type': 'SCRIPT',
                'attributes': [
                    {'type': 'src', 'value': 'https://momentjs.com/downloads/moment-timezone-with-data.js'},
                ]
            },
            { // GOOGLE ANALYTICS
                'type': 'SCRIPT',
                'attributes': [
                    {'type': 'src', 'value': 'https://www.googletagmanager.com/gtag/js?id=UA-115578142-1'},
                    {'type': 'async', 'value': 'true'},
                ]
            },
            { // CHART JS
                'type': 'SCRIPT',
                'attributes': [
                    {'type': 'src', 'value': 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.js'},
                ]
            },
            { // FAVICONS
                'type': 'LINK',
                'attributes': [
                    {'type': 'rel', 'value': 'apple-touch-icon'},
                    {'type': 'sizes', 'value': '57x57'},
                    {'type': 'href', 'value': getPathname() + 'assets/favicon/apple-icon-57x57.png'},
                ]
            },
            {
                'type': 'LINK',
                'attributes': [
                    {'type': 'rel', 'value': 'apple-touch-icon'},
                    {'type': 'sizes', 'value': '60x60'},
                    {'type': 'href', 'value': getPathname() + 'assets/favicon/apple-icon-60x60.png'},
                ]
            },
            {
                'type': 'LINK',
                'attributes': [
                    {'type': 'rel', 'value': 'apple-touch-icon'},
                    {'type': 'sizes', 'value': '72x72'},
                    {'type': 'href', 'value': getPathname() + 'assets/favicon/apple-icon-72x72.png'},
                ]
            },
            {
                'type': 'LINK',
                'attributes': [
                    {'type': 'rel', 'value': 'apple-touch-icon'},
                    {'type': 'sizes', 'value': '76x76'},
                    {'type': 'href', 'value': getPathname() + 'assets/favicon/apple-icon-76x76.png'},
                ]
            },
            {
                'type': 'LINK',
                'attributes': [
                    {'type': 'rel', 'value': 'apple-touch-icon'},
                    {'type': 'sizes', 'value': '114x114'},
                    {'type': 'href', 'value': getPathname() + 'assets/favicon/apple-icon-114x114.png'},
                ]
            },
            {
                'type': 'LINK',
                'attributes': [
                    {'type': 'rel', 'value': 'apple-touch-icon'},
                    {'type': 'sizes', 'value': '120x120'},
                    {'type': 'href', 'value': getPathname() + 'assets/favicon/apple-icon-120x120.png'},
                ]
            },
            {
                'type': 'LINK',
                'attributes': [
                    {'type': 'rel', 'value': 'apple-touch-icon'},
                    {'type': 'sizes', 'value': '144x144'},
                    {'type': 'href', 'value': getPathname() + 'assets/favicon/apple-icon-144x144.png'},
                ]
            },
            {
                'type': 'LINK',
                'attributes': [
                    {'type': 'rel', 'value': 'apple-touch-icon'},
                    {'type': 'sizes', 'value': '152x152'},
                    {'type': 'href', 'value': getPathname() + 'assets/favicon/apple-icon-152x152.png'},
                ]
            },
            {
                'type': 'LINK',
                'attributes': [
                    {'type': 'rel', 'value': 'apple-touch-icon'},
                    {'type': 'sizes', 'value': '180x180'},
                    {'type': 'href', 'value': getPathname() + 'assets/favicon/apple-icon-180x180.png'},
                ]
            },
            {
                'type': 'LINK',
                'attributes': [
                    {'type': 'rel', 'value': 'icon'},
                    {'type': 'sizes', 'value': '192x192'},
                    {'type': 'href', 'value': getPathname() + 'assets/favicon/android-icon-192x192.png'},
                ]
            },
            {
                'type': 'LINK',
                'attributes': [
                    {'type': 'rel', 'value': 'icon'},
                    {'type': 'sizes', 'value': '32x32'},
                    {'type': 'href', 'value': getPathname() + 'assets/favicon/favicon-32x32.png'},
                ]
            },
            {
                'type': 'LINK',
                'attributes': [
                    {'type': 'rel', 'value': 'icon'},
                    {'type': 'sizes', 'value': '96x96'},
                    {'type': 'href', 'value': getPathname() + 'assets/favicon/favicon-96x96.png'},
                ]
            },
            {
                'type': 'LINK',
                'attributes': [
                    {'type': 'rel', 'value': 'icon'},
                    {'type': 'sizes', 'value': '16x16'},
                    {'type': 'href', 'value': getPathname() + 'assets/favicon/favicon-16x16.png'},
                ]
            },
            {
                'type': 'LINK',
                'attributes': [
                    {'type': 'rel', 'value': 'manifest'},
                    {'type': 'href', 'value': getPathname() + 'assets/favicon/manifest.json'},
                ]
            },
            {
                'type': 'META',
                'attributes': [
                    {'type': 'name', 'value': 'msapplication-TileColor'},
                    {'type': 'content', 'value': '#ffffff'},
                ]
            },
            {
                'type': 'META',
                'attributes': [
                    {'type': 'name', 'value': 'msapplication-TileImage'},
                    {'type': 'content', 'value': '/ms-icon-144x144.png'},
                ]
            },
            {
                'type': 'META',
                'attributes': [
                    {'type': 'name', 'value': 'theme-color'},
                    {'type': 'content', 'value': '#ffffff'},
                ]
            },
            { // BOOTSTRAP
                'type': 'LINK',
                'attributes': [
                    {'type': 'rel', 'value': 'stylesheet'},
                    {'type': 'href', 'value': getPathname() + 'assets/bootstrap/css/bootstrap.min.css'},
                ]
            },
            {
                'type': 'SCRIPT',
                'attributes': [
                    {'type': 'src', 'value': getPathname() + 'assets/bootstrap/js/bootstrap.min.js'},
                ]
            },
            {
                'type': 'SCRIPT',
                'attributes': [
                    {'type': 'src', 'value': getPathname() + 'assets/bootstrap/js/bootstrap-theme.min.js'},
                ]
            },
            { // CUSTOM LABS STYLESHEET
                'type': 'LINK',
                'attributes': [
                    {'type': 'rel', 'value': 'stylesheet'},
                    {'type': 'href', 'value': getPathname() + 'stylesheet.css'},
                ]
            },
        ];
        var newElement = document.createElement(headElements[i].type);
        //console.log(favicons[i]);
        //headElements[i].attributes.push({'type': 'onload', 'value': 'buildHtmlHeader('+(i+1)+')'});
        for(j = 0; j < headElements[i].attributes.length; j++){
            var newAttribute = document.createAttribute(headElements[i].attributes[j].type);
            newAttribute.value = headElements[i].attributes[j].value;
            newElement.setAttributeNode(newAttribute);
        }
        header[0].appendChild(newElement);
        
        if (i == (headElements.length-1)){return true;} else {buildHtmlHeader(i+1);}
        /*
        for(i = 0; i < headElements.length; i++){
            var newElement = document.createElement(headElements[i].type);
            //console.log(favicons[i]);
            for(j = 0; j < headElements[i].attributes.length; j++){
                var newAttribute = document.createAttribute(headElements[i].attributes[j].type);
                newAttribute.value = headElements[i].attributes[j].value;
                newElement.setAttributeNode(newAttribute);
            }
            header[0].appendChild(newElement);
        }*/
    }
    
    // RETRIEVE GET PARAMS
    function getQueryVariable(variable) {
        var query = window.location.search.substring(1);
        var vars = query.split("&");
        for (var i=0;i<vars.length;i++) {
                var pair = vars[i].split("=");
                if(pair[0] === variable){return (pair[1].replace(/%20|%2C|[+]|[*+?^${}()|[\]\\]/gi,' ')).replace('+',' ');}
        }
        return(false);
    }
    
    // GET CALL & CALLBACK
    function get(yourUrl, callback){
        console.log(yourUrl);
        if((yourUrl.indexOf('labs.t') > -1 && yourUrl.indexOf('https') == -1) || yourUrl.indexOf('../') == -1){
            //yourUrl = 'https://labs.tssands.com/' + yourUrl;
            console.log('after');
            console.log(yourUrl);
        } else {
            console.log('raw');
            console.log(yourUrl);
        }
        var xmlHttp = new XMLHttpRequest();
        xmlHttp.onreadystatechange = function() { 
            if (xmlHttp.readyState == 4 && xmlHttp.status == 200){
                //console.log(xmlHttp.responseText);
                //console.log('calling');
                callback(JSON.parse(xmlHttp.responseText));
            }
        }
        //alert(yourUrl);
        xmlHttp.open("GET", yourUrl, true); // true for asynchronous 
        xmlHttp.send(null);
     }
     
     // POST DATA & CALLBACK
    function post(yourUrl, postData, callback){
        //console.log(postData);
        postData = JSON.stringify(postData);
        var xmlHttp = new XMLHttpRequest();
        xmlHttp.onreadystatechange = function() { 
            if (xmlHttp.readyState == 4 && xmlHttp.status == 200){
                console.log(xmlHttp.responseText);
                callback(JSON.parse(xmlHttp.responseText));
            }
        }
        xmlHttp.open("POST", yourUrl, true); // true for asynchronous 
        xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xmlHttp.send(postData);
    }

    //SET COOKIE
    function setCookie(cname, cvalue, exdays) {
        //alert('Welcome');
        var d = new Date();
        d.setTime(d.getTime() + (exdays*24*60*60*1000));
        var expires = "expires="+ d.toUTCString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    }
    
    //GET COOKIE
    function getCookie(cname) {
        var name = cname + "=";
        var decodedCookie = decodeURIComponent(document.cookie);
        var ca = decodedCookie.split(';');
        for(var i = 0; i <ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
            }
        }
        return "";
    }

    // ALERT BAR
    function alertBar(alertText, alertColor){
        //loadingStatus('off');
        if(!document.getElementById('alertBarContainer')){
            addElementToDocument('div', [{attributeType: 'id', attributeValue: 'alertBarContainer'}]);
            document.getElementById('alertBarContainer').innerHTML = '<div class="alert alert-danger" id="alertBar" role="alert">\n\
                <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>\n\
                <span class="sr-only">Error:</span><span id="alertText">Alert text...</span>\n\
                <span id="alertTime" style="font-style: italic; margin-left: 1em;"></span>  \n\
            </div>';
        }
        var alertBar = document.getElementById('alertBar');
        var alertBarContainer = document.getElementById('alertBarContainer');
        var alertTextContainer = document.getElementById('alertText');
        var alertTimeContainer = document.getElementById('alertTime');
        if(alertColor == 'red'){
            alertBar.className = "alert alert-danger";
        } else if(alertColor == 'yellow'){
            alertBar.className = "alert alert-warning";
        } else if(alertColor == 'blue'){
            alertBar.className = "alert alert-info";
        } else if(alertColor == 'green'){
            alertBar.className = "alert alert-success";
        }

        alertTextContainer.innerHTML = alertText;
        alertTimeContainer.innerHTML = moment().format('HH:mm:ss');
        alertBarContainer.style.bottom = '0px';
        setTimeout(function(){document.getElementById('alertBarContainer').style.bottom = '-100px';}, 10000);
    }

    // SET LOADING STATUS WINDOW
    function loadingStatus(action){
        if(document.getElementById('loadingStatusContainer')){
            var loadingStatusContainer = document.getElementById('loadingStatusContainer');
            if(action == "on"){
                //console.log('loading spinner on');
                document.getElementById('loadingStatusContainer').style.display = 'block';
            } else {
                //console.log('loading spinner off');
                document.getElementById('loadingStatusContainer').style.display = 'none';
            }
        } else {
            addElementToDocument('div', [
                {'attributeType': 'id', 'attributeValue': 'loadingStatusContainer'},
                {'attributeType': 'class', 'attributeValue': 'loadingPanel'}
            ]);
            document.getElementById('loadingStatusContainer').innerHTML = "<h1>Loading...</h1><br><center><div class='loader'></div></center>";
            loadingStatus(action);
        }
    }

    // ADD ELEMENT TO DIV
    function addElement(parentElement, elementType, elementAttributes){
            //console.log('creating div');
            var newElement = document.createElement(elementType);
            for(ae = 0; ae < elementAttributes.length; ae++){
                var newAttribute = document.createAttribute(elementAttributes[ae].attributeType);
                newAttribute.value = elementAttributes[ae].attributeValue;
                newElement.setAttributeNode(newAttribute);
            }
            document.getElementById(parentElement).appendChild(newElement);
    }
    // ADD ELEMENT TO END OF DOCUMENT
    function addElementToDocument(elementType, elementAttributes){
            //console.log('creating div');
            var newElement = document.createElement(elementType);
            for(ae = 0; ae < elementAttributes.length; ae++){
                var newAttribute = document.createAttribute(elementAttributes[ae].attributeType);
                newAttribute.value = elementAttributes[ae].attributeValue;
                newElement.setAttributeNode(newAttribute);
            }
            document.body.appendChild(newElement);
    }

    //ENCODE  and DECODE FOR URL
           
    function queryEncode(qry){
            qry = encodeURI(qry);
            qry = qry.replace(/\(/g, "%28");
            qry = qry.replace(/\)/g, "%29");
            qry = qry.replace(/\*/g, "%2A");
            qry = qry.replace(/\+/g, "%2B");
            qry = qry.replace(/\:/g, "%3A");

            return qry;
    }
    function queryDecode(qry){
            qry = decodeURI(qry);
            qry = qry.replace(/%28/g, "(");
            qry = qry.replace(/%29/g, ")");
            qry = qry.replace(/%2A/g, "*");
            qry = qry.replace(/%2B/g, "+");
            qry = qry.replace(/%3A/g, ":");

            return qry;
    }
    

    //GET COLOR GRADIENT
    function getGradient(){
        return ["green", "#365CB1", "#365C44", "red", "#005C00", "#36C9D2", 'orange', 'lightgreen', 'lightblue', 'pink', 'darkblue', '#333', '#ccc', 'darkgreen', 'lightgreen', 'purple', '#666'];
    }

    //PRESENT NUMBERS WITH COMMAS
    function numberWithCommas(x) {
        if(x){
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        } else {
            return 0;
        }
    }
       
    function formatStatus(status){
        if(status == "loaded_event"){status = "Loaded";
        } else if(status == "dispatch"){status = "Dispatched";
        } else if(status == "job_requested"){status = "Job Offered";
        } else if(status == "at_pull_point"){status = "At Pull Point";
        } else if(status == "at_staging_area"){status = "Staged";
        } else if(status == "called_to_well_site"){status = "Called to Well";
        } else if(status == "at_well_site"){status = "At Well Site";}

        return status;
    }
    
    // CONVERT ASSOCIATE JSON TO ARRAY
    
    function convertJsonToArray(data){
        return Object.keys(data).map(function(k) { return data[k] });
    }
    
    // RANDOM INT UP TO #
    function getRandomInt(max) {
        return Math.floor(Math.random() * Math.floor(max));
    }
       
    //PRESENT JSON AS HTML TABLE
    var listDataDownload;
    function CreateTableFromJSON(jsonData, openButton = true, target = "listData") {
        listDataDownload = jsonData;
        // EXTRACT VALUE FOR HTML HEADER.
        var col = [];
        if(openButton){col.push('Open');}
        for (var i = 0; i < jsonData.length; i++) {
            for (var key in jsonData[i]) {
                if (col.indexOf(key) === -1) {
                    col.push(key);
                }
            }
        }

        // CREATE DYNAMIC TABLE.
        var table = document.createElement("table");

        // CREATE HTML TABLE HEADER ROW USING THE EXTRACTED HEADERS ABOVE.

        var tr = table.insertRow(-1);                   // TABLE ROW.

        for (var i = 0; i < col.length; i++) {
            var th = document.createElement("th");      // TABLE HEADER.
            th.innerHTML = col[i];
            tr.appendChild(th);
        }

        // ADD JSON DATA TO THE TABLE AS ROWS.
        for (var i = 0; i < jsonData.length; i++) {

            tr = table.insertRow(-1);

            //var tabCell = tr.insertCell(-1);
            //tabCell.innerHTML = jsonData[i].link;
            for (var j = 0; j < col.length; j++) {
                var tabCell = tr.insertCell(-1);
                tabCell.innerHTML = jsonData[i][col[j]];
            }
        }

        // FINALLY ADD THE NEWLY CREATED TABLE WITH JSON DATA TO A CONTAINER.
        var divContainer = document.getElementById(target);
        divContainer.innerHTML = "";
        // divContainer.innerHTML += '<button onclick="JSONToCSVConvertor(listDataDownload, \'list_data\',\'export\')" class="btn-primary" style="margin: 10px;">Download as CSV</button>';
        divContainer.appendChild(table);
        divContainer.innerHTML += '<button onclick="JSONToCSVConvertor(listDataDownload, \'list_data\',\'export\')" class="btn-primary" style="margin: 10px;">Download as CSV</button>';
    }
    
    //CHECK IF VARIABLE IS JSON
    function isJson(str) {
        try {
            JSON.parse(str);
        } catch (e) {
            return false;
        }
        return true;
    }
    
    //CONVERT JSON FILES TO CSV
    function JSONToCSVConvertor(JSONData, ReportTitle, ShowLabel) {
        //console.log(JSONData);
        //alert(JSON.stringify(JSONData));
        //If JSONData is not an object then JSON.parse will parse the JSON string in an Object
        var arrData = typeof JSONData != 'object' ? JSON.parse(JSONData) : JSONData;

        var CSV = '';    
        //Set Report title in first row or line

        CSV += ReportTitle + '\r\n\n';

        //This condition will generate the Label/Header
        if (ShowLabel) {
            var row = "";

            //This loop will extract the label from 1st index of on array
            for (var index in arrData[0]) {

                //Now convert each value to string and comma-seprated
                row += index + ',';
            }

            row = row.slice(0, -1);

            //append Label row with line break
            CSV += row + '\r\n';
        }

        //1st loop is to extract each row
        for (var i = 0; i < arrData.length; i++) {
            var row = "";

            //2nd loop will extract each column and convert it in string comma-seprated
            for (var index in arrData[i]) {
                if(arrData[i][index] == null){arrData[i][index] = "";}
                row += '"' + arrData[i][index] + '",';
            }

            row.slice(0, row.length - 1);

            //add a line break after each row
            CSV += row + '\r\n';
        }

        if (CSV == '') {        
            alert("Invalid data");
            return;
        }   

        //Generate a file name
        var fileName = ("EnHome_");
        //this will remove the blank-spaces from the title and replace it with an underscore
        fileName += ReportTitle.replace(/ /g,"_");   

        //Initialize file format you want csv or xls
        var uri = 'data:text/csv;charset=utf-8,' + escape(CSV);

        // Now the little tricky part.
        // you can use either>> window.open(uri);
        // but this will not work in some browsers
        // or you will not get the correct file extension    

        //this trick will generate a temp <a /> tag
        var link = document.createElement("a");    
        link.href = uri;

        //set the visibility hidden so it will not effect on your web-layout
        link.style = "visibility:hidden";
        link.download = fileName + ".csv";

        //this part will append the anchor tag and remove it after automatic click
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

// CHART.JS FUNCTIONS
    function getDefaultBarChartOptions(){
        return  {
            title:{
                display:false,
                text:""
            },
            tooltips: {
                mode: 'index',
                intersect: false
            },
            legend: {
                labels: {
                    defaultFontSize: 12,
                    fontSize: 12,
                }
            },
            responsive: true,
            scales: {
                xAxes: [{
                    stacked: true,
                    display: true,
                    ticks: {
                        beginAtZero: false,
                    },
                    scaleLabel: {
                        display: false,
                        labelString: 'Silos'
                    }
                }],
                yAxes: [{
                    stacked: true,
                    display: true,
                    ticks: {
                        beginAtZero: false,
                    },
                    scaleLabel: {
                        display: false,
                        labelString: 'Tons'
                    }
                }]
            },
            animation: {
                duration: 1000,
            }
        };
    }
    function getDefaultLineChartOptions(){
        return {
            responsive: true,
            title:{
                display:false,
                text:''
            },
            tooltips: {
                mode: 'index',
                intersect: false,
            },
            legend: {
                labels: {
                    defaultFontSize: 12,
                    fontSize: 12,
                }
            },
            hover: {
                mode: 'nearest',
                intersect: true
            },
            scales: {
                xAxes: [{
                    display: true,
                    scaleLabel: {
                        display: true,
                        labelString: 'Month'
                    },
                    ticks:{display: true},
                }],
                yAxes: [{
                    display: true,
                    scaleLabel: {
                        display: true,
                        labelString: 'Value'
                    },
                    ticks:{display: true},
                }]
            },
            animation: {
                duration: 1000,
            }
        };
    }
    function getDefaultDoughnutChartOptions(){
        return {
            responsive: true,
            title:{
                display:false,
                text:''
            },
            tooltips: {
                mode: 'index',
                intersect: false,
            },
            legend: {
                labels: {
                    defaultFontSize: 12,
                    fontSize: 12,
                }
            },
            hover: {
                mode: 'nearest',
                intersect: true
            },
            cutoutPercentage: 50,
            animation: {
                animateRotate: true,
                animateScale: true
            }
        };
    }
    function getDefaultPieChartOptions(){
        return {
            responsive: true,
            title:{
                display:false,
                text:''
            },
            tooltips: {
                mode: 'index',
                intersect: false,
            },
            hover: {
                mode: 'nearest',
                intersect: true
            },
            cutoutPercentage: 0,
            animation: {
                animateRotate: true,
                animateScale: true
            }
        };
    }

    // RENDER/UPDATE A NEW CHART
    function newChartJs(chartTitle, chartSubTitle, chartTargetId, chartData, chartType, chartOptions){
        //console.log('called');
        var chartFound = false;
        var chartTarget = document.getElementById(chartTargetId);
        var chartTitleNoSpace = chartTitle.replace(/\s/g, '');
        var chartId = chartTitleNoSpace + "Canvas";
        var defaultBarChartOptions  = getDefaultBarChartOptions();
        var defaultLineChartOptions  = getDefaultLineChartOptions();
        var defaultDoughnutChartOptions  = getDefaultDoughnutChartOptions();
        var defaultPieChartOptions  = getDefaultPieChartOptions();
        
        //DEFAULTS
            if(chartType == null){chartType = "bar";}
            if(chartOptions == null){
                if(chartType == 'bar' || chartType == 'horizontalBar'){
                    chartOptions = JSON.parse(JSON.stringify(defaultBarChartOptions));
                } else if (chartType == 'line'){
                    chartOptions = JSON.parse(JSON.stringify(defaultLineChartOptions));
                } else if (chartType == 'doughnut'){
                    chartOptions = JSON.parse(JSON.stringify(defaultDoughnutChartOptions));
                } else if (chartType == 'pie'){
                    chartOptions = JSON.parse(JSON.stringify(defaultPieChartOptions));
                }
                /*chartOptions.tooltips = {
                        enabled: false,
                        custom: customTooltips,
                        mode: 'nearest',
                        intersect: true,
                        backgroundColor: 'black',
                        titleFontFamily: 'Arial',
                        titleFontSize: 20,
                        titleFontStyle: 'underline',
                        titleFontColor: 'blue',
                        titleSpacing: 10,
                        titleMarginBottom: 1,
                        bodyFontFamily: 'Arial',
                        bodyFontSize: 20,
                        bodyFontStyle: 'underline',
                        bodyFontColor: 'blue',
                        bodySpacing: 10,
                        footerFontFamily: 'Arial',
                        footerMarginTop: 5,
                        xPadding: 30,
                        yPadding: 5,
                        caretPadding: 20,
                        caretSize: 10,
                        cornerRadius: 30,
                        displayColors: true,
                        borderColor: "green",
                        borderWidth: 2,
                        /*callbacks: {
                            beforeTitle: function(tooltipItem, data){return 'beforeTitle'},
                            title: function(tooltipItem, data){return 'title'},
                            afterTitle: function(tooltipItem, data){return 'afterTitle'},
                            label: function(tooltipItem, data){
                                var index = tooltipItem.index;
                                return (JSON.stringify(tooltipItem))
                            },
                            labelColor: function(tooltipItem, chart) {
                                return {
                                    borderColor: 'rgb(255, 0, 0)',
                                    backgroundColor: 'rgb(255, 0, 0)'
                                }
                            },
                            labelTextColor:function(tooltipItem, chart){
                                return '#543453';
                            }
                        },*/
                    //};
            }
            if(chartData == null){console.log('ERROR: No Chart Data');}
            
        // CHECK IF EXISTS
            if(document.getElementById(chartTitleNoSpace + "Canvas")){
                chartFound = true;
                chartCanvas = document.getElementById(chartId);
            } 
            
        // IF CHART ALREADY EXISTS
            if (chartFound == false){
                //CREATE DIV
                //console.log('creating div');
                var chartContainer = document.createElement('div');
                var newAttribute = document.createAttribute('id');
                newAttribute.value = chartTitleNoSpace + "Container";
                chartContainer.setAttributeNode(newAttribute);
                var newAttribute = document.createAttribute('class');
                newAttribute.value = "chartJsItem";
                chartContainer.setAttributeNode(newAttribute);
                chartTarget.appendChild(chartContainer);

                chartContainer.innerHTML = "<h2>" + chartTitle + "</h2><h3><i>" + chartSubTitle + "</i></h3>";

                //CREATE CANVAS
                //console.log('creating canvas');
                var chartCanvas = document.createElement('canvas');
                var newAttribute = document.createAttribute('id');
                newAttribute.value = chartId;
                chartCanvas.setAttributeNode(newAttribute);
                chartTarget.appendChild(chartCanvas);
            }
            
        //RENDER CHART
            //console.log('Rendering Chart');
            //console.log(chartOptions);
            window[chartTitleNoSpace] = new Chart(chartCanvas.getContext("2d"), {type: chartType, options: chartOptions, data: chartData});
            window[chartTitleNoSpace].update(); 
    }
    
    function addDoughnutKeyNumber(chartTitle){
        var chartTitleNoSpace = chartTitle.replace(/\s/g, '');
        var container = document.getElementById(chartTitleNoSpace + "Container");
        var chartItem = pendingSqlAsChart.find(function(item, i){
            return (item.chartTitle == chartTitle);
        });
        var keys = Object.keys(chartItem.chartData[0]);
        var keyNumber = chartItem.chartData[0][keys[0]];
        //console.log(keyNumber);
        
        container.innerHTML += "<span style='position: absolute; top: 50%; left: 30%; font-size: 5em;'>"+keyNumber+"</span>";
    }
    
    /*var customTooltips = function(tooltip) {
        // Tooltip Element
        var tooltipEl = document.getElementById('chartjs-tooltip');

        if (!tooltipEl) {
                tooltipEl = document.createElement('div');
                tooltipEl.id = 'chartjs-tooltip';
                tooltipEl.innerHTML = "<table></table>"
                this._chart.canvas.parentNode.appendChild(tooltipEl);
        }

        // Hide if no tooltip
        if (tooltip.opacity === 0) {
                tooltipEl.style.opacity = 0;
                return;
        }

        // Set caret Position
        tooltipEl.classList.remove('above', 'below', 'no-transform');
        if (tooltip.yAlign) {
                tooltipEl.classList.add(tooltip.yAlign);
        } else {
                tooltipEl.classList.add('no-transform');
        }

        function getBody(bodyItem) {
                return bodyItem.lines;
        }

        // Set Text
        if (tooltip.body) {
                var titleLines = tooltip.title || [];
                var bodyLines = tooltip.body.map(getBody);

                var innerHtml = '<thead>';

                titleLines.forEach(function(title) {
                        innerHtml += '<tr><th>' + title + '</th></tr>';
                });
                innerHtml += '</thead><tbody>';

                bodyLines.forEach(function(body, i) {
                        var colors = tooltip.labelColors[i];
                        var style = 'background:' + colors.backgroundColor;
                        style += '; border-color:' + colors.borderColor;
                        style += '; border-width: 2px'; 
                        var span = '<span class="chartjs-tooltip-key" style="' + style + '"></span>';
                        innerHtml += '<tr><td>' + span + body + '</td></tr>';
                });
                innerHtml += '</tbody>';

                var tableRoot = tooltipEl.querySelector('table');
                tableRoot.innerHTML = innerHtml;
        }

        var positionY = this._chart.canvas.offsetTop;
        var positionX = this._chart.canvas.offsetLeft;

        // Display, position, and set styles for font
        tooltipEl.style.opacity = 1;
        tooltipEl.style.left = positionX + tooltip.caretX + 'px';
        tooltipEl.style.top = positionY + tooltip.caretY + 'px';
        tooltipEl.style.fontFamily = tooltip._fontFamily;
        tooltipEl.style.fontSize = tooltip.fontSize;
        tooltipEl.style.fontStyle = tooltip._fontStyle;
        tooltipEl.style.padding = tooltip.yPadding + 'px ' + tooltip.xPadding + 'px';
    };
    
    function tooltipContent(i, data){
        return {
            label: 'test'
        }
    }*/

    // CREATE A CHART FROM A SQL QUERY
    var pendingSqlAsChart = [];
    function getSqlAsChart(chartTitle, chartSubtitle, chartTarget,  chartQuery, chartType, chartOptions, chartGradient){
        pendingSqlAsChart.push({
            chartTitle: chartTitle,
            chartSubtitle: chartSubtitle,
            chartTarget: chartTarget,
            chartQuery: chartQuery,
            chartType: chartType,
            chartOptions: chartOptions,
            chartData: [],
            chartGradient: chartGradient
        });
        //console.log(getPathname()+"reporter/api/openquery/?key=" + userKey + "&q=" + chartQuery);
        //console.log(chartQuery);
        get(getPathname()+"reporter/api/openquery/?key=" + userKey + "&q=" + chartQuery, renderSqlAsChart);
    }

    function renderSqlAsChart(data){
        var pendingIndex = pendingSqlAsChart.find(function(item, i){
            return (item.chartQuery == data.result.detail.query);
        });
        var i = pendingSqlAsChart.indexOf(pendingIndex);
        pendingSqlAsChart[i].chartData = data.data;
        //console.log(pendingSqlAsChart[i]);
        var sqlData = data.data;
        var chartLabels = [];
        var chartDatasets = [];
        var gradient;
        var columns = (Object.keys(sqlData[0]));
        
        // SET GRADIENT
        if(pendingIndex.chartGradient){
            console.log('custom gradient');
            console.log(gradient);
            gradient = pendingIndex.chartGradient;
        } else {
            gradient =  getGradient();
        }
        


        // BAR CHART
        if(pendingIndex.chartType == 'bar' || pendingIndex.chartType == 'horizontalBar'){
            if(pendingIndex.chartOptions == null){pendingIndex.chartOptions = getDefaultBarChartOptions();}
             
            if(pendingIndex.chartType == 'bar' ){
                pendingIndex.chartOptions.scales.xAxes[0].scaleLabel.display = true;
                pendingIndex.chartOptions.scales.xAxes[0].scaleLabel.labelString = columns[0];
                pendingIndex.chartOptions.scales.yAxes[0].scaleLabel.display = false;
                pendingIndex.chartOptions.scales.yAxes[0].scaleLabel.labelString = "";
            } else if (pendingIndex.chartType == 'horizontalBar'){
                pendingIndex.chartOptions.scales.xAxes[0].scaleLabel.display = false;
                pendingIndex.chartOptions.scales.xAxes[0].scaleLabel.labelString = "";
                pendingIndex.chartOptions.scales.yAxes[0].scaleLabel.display = true;
                pendingIndex.chartOptions.scales.yAxes[0].scaleLabel.labelString = columns[0];
            }

            for(i = 0; i < sqlData.length; i++){
                chartLabels.push(sqlData[i][columns[0]]);
            }
             // BUILD BAR DATASETS
             for(j=1; j < columns.length; j++){ // CYCLE THROUGH COLUMNS
                 var itemKey = columns[j];
                 var datasetData = [];
                 for(i = 0; i < sqlData.length; i++){ // CYCLE THROUGH ROWS
                     datasetData.push(sqlData[i][itemKey]);
                 }
                 chartDatasets.push({
                      label: columns[j],
                      data: datasetData,
                      backgroundColor: gradient[j-1],
                      borderColor: "#fff",
                      borderWidth: 1
                 });
             }
             
             chartData = {
                  labels: chartLabels,
                  datasets: chartDatasets
              };
        } else if (pendingIndex.chartType == 'line'|| pendingIndex.chartType == 'time' || pendingIndex.chartType == 'time-hour' || pendingIndex.chartType == 'time-day'){
            if(pendingIndex.chartOptions == null){pendingIndex.chartOptions = getDefaultLineChartOptions();}

            pendingIndex.chartOptions.scales.xAxes[0].scaleLabel.display = true;
            pendingIndex.chartOptions.scales.xAxes[0].scaleLabel.labelString = columns[0];
            pendingIndex.chartOptions.scales.yAxes[0].scaleLabel.display = true;
            pendingIndex.chartOptions.scales.yAxes[0].scaleLabel.labelString = columns[1];

            if(pendingIndex.chartType == 'time' || pendingIndex.chartType == 'time-hour' || pendingIndex.chartType == 'time-day'){
                //console.log('TIMELINE');
                if(pendingIndex.chartType == 'time-day'){
                    var timeUnit = 'day';
                    var timeFormat = 'M/D';
                } else {
                    var timeUnit = 'hour';
                    var timeFormat = 'M/D H:mm';
                }
                pendingIndex.chartOptions.scales.xAxes[0].type = "time";
                pendingIndex.chartOptions.scales.xAxes[0].time = {
                    unit: timeUnit,
                    displayFormats: {
                        hour: timeFormat
                }};
                
                for(tl = 0; tl < sqlData.length; tl++){
                    sqlData[tl][columns[0]] = moment(sqlData[tl][columns[0]]).tz('America/Chicago');
                    //console.log(sqlData[tl][columns[0]]);
                }
                pendingIndex.chartType = 'line';
            }

            // BUILD LINE DATASETS
            for(j=1; j < columns.length; j++){ // CYCLE THROUGH COLUMNS
                var itemKey = columns[j];
                var datasetData = [];
                for(i = 0; i < sqlData.length; i++){ // CYCLE THROUGH ROWS
                    datasetData.push({x: sqlData[i][columns[0]], y: sqlData[i][columns[j]]});
                }
                
                chartDatasets.push({
                     label: columns[j],
                     data: datasetData,
                     backgroundColor: gradient[j],
                     borderColor: gradient[j],
                     fill: false,
                     borderWidth: 1
                });
            }
            chartData = {
                 datasets: chartDatasets
            };
             
        } else if (pendingIndex.chartType == 'doughnut' || pendingIndex.chartType == 'pie'){
            if(pendingIndex.chartOptions == null){
                if (pendingIndex.chartType == 'doughnut' ){pendingIndex.chartOptions = getDefaultDoughnutChartOptions();
                } else if (pendingIndex.chartType == 'pie' ){pendingIndex.chartOptions = getDefaultPieChartOptions();}
            }

            // BUILD LINE DATASETS
            for(i=0; i < sqlData.length; i++){ // CYCLE THROUGH COLUMNS
                var datasetData = [];
                for(j = 0; j < columns.length; j++){ // CYCLE THROUGH ROWS
                    datasetData.push(sqlData[i][columns[j]]);
                }
                
                chartDatasets.push({
                     data: datasetData,
                     backgroundColor: gradient,
                     borderColor: "#fff",
                     borderWidth: 1
                });
            }
            chartData = {
                labels: columns,
                datasets: chartDatasets
            };
             
        }
        newChartJs(pendingIndex.chartTitle, pendingIndex.chartSubtitle, pendingIndex.chartTarget, chartData, pendingIndex.chartType, pendingIndex.chartOptions);
        if(pendingIndex.chartType == 'doughnut'){
            //addDoughnutKeyNumber(pendingIndex.chartTitle);
        }
     }
    //getSqlAsChart('Line Test', 'POs', 'welcomeChartsContainer', 'SELECT id, measured_weight, weight FROM truck_loads WHERE measured_weight IS NOT NULL', 'line');

    
// SECURITY

    function loginCheck(){
        setCookie('token','1234');
        setCookie('userId','444');
        setCookie('userName','johnny tester 123');
        setCookie('firstName','John');
        setCookie('lastName','Tester');
        setCookie('email','john.sturdivant@gmail.com');
        
        
        userInfo = {
            "token": getCookie('token'),
            "userId": getCookie('userId'),
            "userName": getCookie('userName'),
            "firstName": getCookie('firstName'),
            "lastName": getCookie('lastName'),
            "email": getCookie('email'),
        };
        
        
        /*
        if(getCookie('privs') == '' || getCookie('username') == '' || getCookie('key') == '' || getCookie('key') == null || getCookie('key') == false){
            if(getQueryVariable('key') == false){ 
                window.location.href = getPathname()+ "login.html?url=" + window.location.pathname;
                window.location.replace(getPathname()+ "login.html?url=" + window.location.pathname);
                return false;
            } else {
                loginFromUrl();
                return true;
            }
        }
        */
    }
    
    function login(data = null){
        var userKey, username, pw;
        var loginAlert = document.getElementById('loginAlert');
        if(getQueryVariable('url') == '\\'){
            var newUrl = 'index.html';
        } else if(getQueryVariable('url')){
            var newUrl = getQueryVariable('url');
        } else {
            var newUrl = 'index.html';
        }
        if(data){
            console.log('data');
            loadingStatus('off');
            if(data.result.result == "success"){
                alertBar('Success!', 'green');
                userKey = data.data.key;
                setCookie('key',userKey,14);
                setCookie('role',data.data.role,14);
                setCookie('company',data.data.company,14);
                setCookie('cid',data.data.customer_id,14);
                setCookie('privs',JSON.stringify(data.data.privs),14);
                setCookie('username',data.result.detail.username,14);
                setCookie('customer',data.result.detail.customer,14);
                alertBar('Sending you to '+newUrl, 'green');
                alertBar(getCookie('key'), 'green');
                window.location.href = newUrl;
                window.location.replace(newUrl);
                //window.location.href = 'index.html';
                //window.location.replace('index.html');
            } else {
                console.log('fail');
                alertBar('Login failed. Please try again.', 'red');
                document.getElementById('loginContainer').style.display = 'block';
                return false;
            }
        } else {
            console.log('no data');
            setCookie('key','',0);
            setCookie('role','',0);
            setCookie('company','',0);
            setCookie('cid','',0);
            setCookie('privs','',0);
            setCookie('username','',0);
            setCookie('customer','',0);
            console.log('no key');
            loadingStatus('on');
            username = document.getElementById('usernameEntry');
            pw = document.getElementById('pwEntry');
            //var url =  "https://labs.tssands.com/general/api/getkey/?username=" + encodeURIComponent(username.value) + "&pw=" + encodeURIComponent(pw.value);
            var url =  getPathname()+"  general/api/getkey/?username=" + encodeURIComponent(username.value) + "&pw=" + encodeURIComponent(pw.value);
            //console.log(url);
            get(url, login);
            pw.value = "";
            return true;
        }
    }
    
    function loginFromUrl(data = null){
        var userKey, username, pw;
        //console.log('loginFromUrl');
        //var loginAlert = document.getElementById('loginAlert');
        if(data){
            if(data.result.result == "success"){
                console.log( "Success!");
                userKey = data.result.detail.key;
                console.log(userKey);
                setCookie('key',userKey,14);
                setCookie('role',data.data.role,14);
                setCookie('company',data.data.company,14);
                setCookie('cid',data.data.customer_id,14);
                setCookie('privs',JSON.stringify(data.data.privs),14);
                setCookie('username',data.result.detail.username,14);
                return true;
                //window.location.href = "http://labs.tssands.com/index.html";
                //window.location.href = "index.html";
                //window.location.replace("index.html");
                //document.getElementById('mainMenu').style.display = 'block';
                //document.getElementById('loginContainer').style.display = 'none';
                //buildEhHeader('Home');
            } else {
                console.log('destroyCookies');
                //setCookie('key','',0);
                //setCookie('username','',0);
                //setCookie('privs','',0);
                //setCookie('role','',0); 
                //loginAlert.style.display='block';
                //loginAlert.innerHTML = "Login failed, please try again";
                //document.getElementById('loginContainer').style.display = 'block';
                return false;
            }
        } else {
            userKey = getCookie('key');
            if(userKey == '' || userKey == null ){
                //var string = getQueryVariable('logincreds');
                //console.log('logging in');
                //var decodedString = JSON.parse(atob(string));
                //console.log(decodedString);
                var key = getQueryVariable('key');
                //setCookie('key',key,14);
                //pw = getQueryVariable('pw');
                //console.log(key);
                //console.log(getPathname()+"general/api/checkkey/?key=" + key);
                get(getPathname()+"general/api/checkkey/?key=" + key, loginFromUrl);
                return true;
            } else {
                //window.location.href = "index.html";
                //window.location.replace("index.html");
                //document.getElementById('mainMenu').style.display = 'block';
                //document.getElementById('loginContainer').style.display = 'none';
            }
        }
    }

    function logout(){
        setCookie('key','',0);
        setCookie('username',null,0);
        setCookie('privs',null,0);
        setCookie('role',null,0); 
        setCookie('cid',null,0); 
        setCookie('company',null,0); 
        setCookie('customer',null,0); 
        
        //userInfo = {};
        
        //loginCheck();
    }    

    function successFunction(data){
        console.log('Success!function');
        console.log(data);
    }

//GLOBAL VARIABLES
    var userKey = getCookie('key');

//ON LOAD
    buildHtmlHeader();
    
    // GOOGLE ANALYTICS
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'UA-115578142-1');