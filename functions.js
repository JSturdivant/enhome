console.log('functions.js loaded');

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
    //loginCheck();
    //BUILD STANDARD HEADER



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
      yourUrl = "http://localhost/enhome/" + yourUrl;
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

    function successFunction(data){
        console.log('Success!function');
        console.log(data);
    }


//ON LOAD

    // GOOGLE ANALYTICS
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'UA-115578142-1');
