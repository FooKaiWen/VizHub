var selectedValue = 50;
var selector = document.getElementById("selected");
var info = document.getElementById("topInfo");
var topLikes;
var friendNum = 1;
var highestCount = 0;
var highestCountType;
var globalLikes = [], globalAccuLikes = [], globalAccuComment = [], globalAccuShare = [], globalDistinctReachDate = [];
var globalLove = [], globalHaha = [], globalWow = [], globalSad = [], globalAngry = [];

var reactChart;
var reachChart;

selector.addEventListener('change', function () {
    if (selectedValue != selector[selector.selectedIndex].value) {
        selectedValue = selector[selector.selectedIndex].value
    }

    if(checkBoxReach.checked){
        reactChart.destroy();
        plotReachChart();
        showLikeInsight();    
    }
    else if(checkBoxReact.checked){
        reachChart.destroy();
        plotReactChart();
        showReactionInsight();
    }

});

function hideInsight() {
    info.innerHTML ="Please toggle the parameters beside for insight!";
}

var triggerMessage = document.getElementById("triggerMessage");
var chartInfo = document.getElementById("chartInfo");

function showInfo(message){
    if(checkBoxType.checked){
        triggerMessage.style.height = '70px';
    } else {
        triggerMessage.style.height = '40px';
    }
    chartInfo.innerHTML = message;
}

function hideInfo() {
    chartInfo.innerHTML = "Please toggle the parameters beside for graph!";
    triggerMessage.setAttribute("style","height:40px");
    triggerMessage.style.height = '40px';
}

var checkBoxReach = document.getElementById("togAllBtn");
var checkBoxReact = document.getElementById("togTotBtn");
var checkBoxType = document.getElementById("togTypBtn");

function showLikeInsight(){
    var interactionRate = ((topLikes/friendNum)*100).toFixed(2);
    if(interactionRate > 20){
        info.innerHTML = "The highest number of likes you have gotten is " + topLikes + "! " + interactionRate + "% of your friends interacted with you! Well Done!";
    } else {
        info.innerHTML = "The highest number of likes you have gotten is " + topLikes + "! Only " + interactionRate + "% of your friends interacted with you. Please keep it up!";
    }
}

function showReactionInsight(){
    var emoji = "";
    if(highestCountType == "Haha"){
        emoji = "😆";
    } else if(highestCountType == "Wow"){
        emoji = "😲";
    } else if(highestCountType == "Love"){
        emoji = "😍";
    } else if(highestCountType == "Angry"){
        emoji = "😡";
    } else if(highestCountType == "Sad"){
        emoji = "😢";
    }
    info.innerHTML = "As an overall, your friends frequently interacted with your posts with <b>" + highestCountType + "</b> " + emoji + " reaction! The number of reaction goes as high as <b>" + highestCount + "</b>!";
}

function showPostTypeInsight(){
    info.innerHTML = "Interesting fact: your <b>" + highestCountType + "</b> type posts <i>(which may/may not be your most used post type)</i> have successfully attracted as much as <b>" + highestCount + "</b> likes!";
}

checkBoxReach.addEventListener('change', function () {
    if (this.checked) {
        showInfo("This line graph shows the number of Likes, Comments and Shares of each post against the posted date.");
        // showInsight();
        showLikeInsight();
    } else if (!this.checked) {
        hideInfo();
        hideInsight();
    }
});

checkBoxReact.addEventListener('change', function () {
    if (this.checked) {
        showInfo("This bar graph shows the total number of Reactions such as Wow, Sad, Angry reaction of each post against the posted date.");
        // showInsight();
        showReactionInsight();
    } else if (!this.checked) {
        hideInfo();
        hideInsight();
    }
});

checkBoxType.addEventListener('change', function () {
    if (this.checked) {
        showInfo("This pie chart shows the accumulated different types of post, up to 50 posts, created by you. *Changing the number of post WILL NOT change this chart as the result is accumulated from your past posts.*");
        // showInsight();
        showPostTypeInsight();
    } else if (!this.checked) {
        hideInfo();
        hideInsight();
    }
});

Chart.defaults.global.defaultFontColor = 'black';
Chart.defaults.global.defaultFontSize = 17;

function assignValues(newLikes, accuLike, accuComment, accuShare, distinctDate, accuLove, accuHaha, accuWow, accuSad, accuAngry){

    globalLikes = newLikes.slice(0,newLikes.length);
    globalAccuLikes = accuLike.slice(0,accuLike.length);
    globalAccuComment = accuComment.slice(0, accuComment.length);
    globalAccuShare = accuShare.slice(0, accuShare.length);
    globalDistinctDate = distinctDate.slice(0, distinctDate.length);

    globalLove = accuLove.slice(0, accuLove.length);
    globalHaha = accuHaha.slice(0, accuHaha.length);
    globalWow = accuWow.slice(0, accuWow.length);
    globalSad = accuSad.slice(0, accuSad.length);
    globalAngry = accuAngry.slice(0, accuAngry.length);
}

function plotReachChart() {

    var tempLikes = [], tempTopLikes = [], tempComment = [], tempShare = [], tempTime = [];
    topLikes = 0;
    var k = selectedValue-1;
    for(i = globalLikes.length-1;i >= (globalLikes.length-1)-selectedValue; i--){
        if(globalLikes[i] > topLikes){
            topLikes = globalLikes[i];
        }
    }
    for (i = globalAccuLikes.length-1; i >= (globalAccuLikes.length-1)-selectedValue; i--) {
        tempLikes[k] = globalAccuLikes[i];
        tempComment[k] = globalAccuComment[i];
        tempShare[k] = globalAccuShare[i];
        tempTime[k] = globalDistinctDate[i];
        k--;
    }

    if (checkBoxReach.checked) {
        if (checkBoxReact.checked) {
            reachChart.destroy();
            checkBoxReact.checked = false;
        }
        if (checkBoxType.checked) {
            postTypeChart.destroy();
            checkBoxType.checked = false;
        }

        var ctx = document.getElementById("chart").getContext('2d');
        reactChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: tempTime,
                datasets: [
                    {
                        label: 'Likes',
                        fill: false,
                        //lineTension: 0.5,
                        data: tempLikes,
                        backgroundColor: 'rgba(72,61,139, 0.2)',
                        borderColor: 'rgba(72,61,139, 1)',
                        borderWidth: 3
                    },
                    {
                        label: 'Comment',
                        data: tempComment,
                        backgroundColor: 'rgba(255,140,0, 0.2)',
                        borderColor: 'rgba(255,140,0, 1)',
                        borderWidth: 3
                    },
                    {
                        label: 'Share',
                        data: tempShare,
                        backgroundColor: 'rgba(178,34,34, 0.2)',
                        borderColor: 'rgba(178,34,34,1)',
                        borderWidth: 3
                    }]
            },
            options: {
                scales: {
                    xAxes: [{
                        gridLines: {
                            color: 'rgba(255, 255, 255,0.5)',
                            lineWidth: 2
                        }
                    }],
                    yAxes: [{
                        gridLines: {
                            color: 'rgba(255, 255, 255,0.5)',
                            lineWidth: 2
                        },
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });
    }
    else if (!checkBoxReach.checked) {
        reactChart.destroy();
    }
}

function sortHighestCount(paramOne, paramTwo, paramThree, paramFour, paramFive,type){
    var obj;
    if(type == "reaction"){
        obj = {
            Love : paramOne, Haha : paramTwo, Wow : paramThree, Sad : paramFour, Angry : paramFive
        };
    } else if(type == "postType"){
        obj = {
            Link : paramOne, Video : paramTwo, Photo : paramThree, Event : paramFour, Status : paramFive
        };
    }  

    var keys = Object.keys(obj);
    var max = keys[0];
    for (var i = 1, n = keys.length; i < n; ++i) {
       var k = keys[i];
       if (obj[k] > obj[max]) {
          max = k;
       }
    }
    highestCount = obj[max];
    highestCountType =  max;
}

function plotReactChart() {

    var tempLove = [], tempHaha = [], tempWow = [], tempSad = [], tempAngry = [], tempTime = [];
    var k = selectedValue-1;
    for (i = globalLove.length-1; i >= (globalLove.length-1)-selectedValue; i--) {
        tempLove[k] = globalLove[i];
        tempHaha[k] = globalHaha[i];
        tempWow[k] = globalWow[i];
        tempSad[k] = globalSad[i];
        tempAngry[k] = globalAngry[i];
        tempTime[k] = globalDistinctDate[i];
        k--;
    }

    if (checkBoxReact.checked) {
        if (checkBoxReach.checked) {
            reactChart.destroy();
            checkBoxReach.checked = false;
        }

        if (checkBoxType.checked) {
            postTypeChart.destroy();
            checkBoxType.checked = false;
        }

        var ctx = document.getElementById("chart").getContext('2d');
        reachChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: tempTime,
                datasets: [{
                    label: 'Love 😍',
                    fill: true,
                    backgroundColor: 'rgba(128,0,0, 0.8)',
                    borderColor: 'rgba(128,0,0, 1)',
                    data: tempLove,
                }, {
                    label: 'Haha 😆',
                    backgroundColor: 'rgba(255,165,0, 0.8)',
                    borderColor: 'rgba(255,165,0, 1)',
                    data: tempHaha,
                    fill: true,

                }, {
                    label: 'Wow 😲',
                    backgroundColor: 'rgba(46,139,87, 0.8)',
                    borderColor: 'rgba(46,139,87, 1)',
                    data: tempWow,
                    fill: true,
                }, {
                    label: 'Sad 😢',
                    backgroundColor: 'rgba(153,50,204, 0.8)',
                    borderColor: 'rgba(153,50,204, 0.8)',
                    data: tempSad,
                    fill: true,
                }, {
                    label: 'Angry 😡',
                    backgroundColor: 'black',
                    borderColor: 'black',
                    data: tempAngry,
                    fill: true,
                }]
            },
            options: {
                scales: {
                    xAxes: [{
                        gridLines: {
                            color: 'rgba(255, 255, 255,0.5)',
                            lineWidth: 2
                        },
                        stacked: true
                    }],
                    yAxes: [{
                        gridLines: {
                            color: 'rgba(255, 255, 255,0.5)',
                            lineWidth: 2
                        },
                        stacked: true
                    }],
                    ticks: {
                        beginAtZero: true,
                    }
                }
            }
        });
    }
    else if (!checkBoxReact.checked) {
        reachChart.destroy();
    }
}

var postTypeChart;
function plotPostTypeChart(chartid, postCount, postType){

    if(checkBoxType.checked){
        if (checkBoxReach.checked) {
            reactChart.destroy();
            checkBoxReach.checked = false;
        }
        if (checkBoxReact.checked) {
            reachChart.destroy();
            checkBoxReact.checked = false;
        }
        var ctx = document.getElementById(chartid).getContext('2d');
        postTypeChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: postCount,
                    backgroundColor: ["Red","Green","Blue","Yellow","Purple","Pink"],
                    hoverBackgroundColor: ["Red","Green","Blue","Yellow","Purple","Pink"],
                    hoverBorderColor: "Black",
                }],
                labels:
                    postType
            },
            options:{
                segmentShowStroke : true,
                segmentStrokeWidth : 1,
                cutoutPercentage : 75,
                animationSteps : 100,
                animationEasing : "easeOutBounce",
                animateRotate : true,
                animateScale : true,
                responsive: true,
                maintainAspectRatio: true,
                showScale: true,
                animateScale: true
            }
        });
    } else if(!checkBoxType.checked){
        postTypeChart.destroy();
    }
}

function friendNumber(x) {
    friendNum = x;
}

// var postpostTypeChart;
// function plotPostTypeChart(chartid, newType, newTime){

//     var tempTime = [];
//     var totLink18 = 0, totStatus18 = 0, totalPhoto18 = 0, totalVideo18 = 0, totalOffer18 = 0;
//     var totLink17 = 0, totStatus17 = 0, totalPhoto17 = 0, totalVideo17 = 0, totalOffer17 = 0;

//     for (i = 0; i < newType.length; i++) {
//         tempTime[i] = newTime[i].slice(0, 4);

//         if(tempTime = "2018"){
//             if(newType[i] == "link"){
//                 totLink18 += 1;
//                 console.log(totLink18);
//             }
//             else if(newType[i] == "status"){
//                 totStatus18 += 1;
//             }
//             else if(newType[i] == "photo"){
//                 totalPhoto18 += 1; 
//             }
//             else if(newType[i] == "video"){
//                 totalVideo18 += 1;
//             }
//             else if(newType[i] == "offer"){
//                 totalOffer18 +=1;
//             }
//         }
//         else{
//             if(newType[i] == "link"){
//                 totLink17 += 1;
//             }
//             else if(newType[i] == "status"){
//                 totStatus17 += 1;
//             }
//             else if(newType[i] == "photo"){
//                 totalPhoto17 += 1; 
//             }
//             else if(newType[i] == "video"){
//                 totalVideo17 += 1;
//             }
//             else if(newType[i] == "offer"){
//                 totalOffer17 +=1;
//             }
//         }
//         if (checkBoxType.checked) {
//             postTypeChart.destroy();
//             checkBoxType.checked = false;
//         }

        
//     }

//     var ctx = document.getElementById(chartid).getContext('2d');
//         postpostTypeChart = new Chart(ctx, {
//             type: 'radar',
//             data: {
//                 labels: ['link', 'status', 'photo', 'video', 'offer'],
//                 datasets: [
//                     {
//                         label: '2018',
//                         data: totLink18, totStatus18, totalPhoto18, totalVideo18, totalOffer18,
//                         backgroundColor: 'rgba(107,142,35,5)',
//                         borderColor: 'rgba(85,107,47,1)',
//                         borderWidth: 3
//                     },{
//                         label: '2017',
//                         data: totLink17, totStatus17, totalPhoto17, totalVideo17, totalOffer17,
//                         backgroundColor: 'rgba(255,165,0, 0.8)',
//                         borderColor: 'rgba(255,165,0, 1)',
//                         borderWidth: 3

//                     }]
//             },
//             options: {
//                 scales: {
//                     xAxes: [{
//                         gridLines: {
//                             color: 'rgba(255, 255, 255,0.5)',
//                             lineWidth: 2
//                         }
//                     }],
//                     yAxes: [{
//                         gridLines: {
//                             color: 'rgba(255, 255, 255,0.5)',
//                             lineWidth: 2
//                         },
//                         ticks: {
//                             beginAtZero: true,
//                         }
//                     }]
//                 }
//             }
//         });



// }

// var allFriendChart;
// var friendNum;
// function plotFriend(chartid, newLikes, newLove, newHaha, newWow, newSad, newAngry, newTime, newFriend) {
//     var totNum = [];
//     var checkbox = document.getElementById("togFriBtn");
//     console.log("hi");

//     var checkBoxReach = document.getElementById("togAllBtn");
//     var checkBoxReact = document.getElementById("togTotBtn");
//     var checkbox_Fri = document.getElementById("togFriBtn");

//     for (var i = 0; i < selectedValue; i++) {
//         totNum[i] = 0;
//         totNum[i] += newLikes[i];
//         totNum[i] += newLove[i];
//         totNum[i] += newHaha[i];
//         totNum[i] += newWow[i];
//         totNum[i] += newSad[i];
//         totNum[i] += newAngry[i];
//         totNum[i] /= newFriend; //

//         // if (totNum[i] < 0.2) {
//         //     totNum[i] *= -1;
//         // }
//         console.log(newLikes[i]);
//         console.log(totNum[i]);
//     }

//     var tempNum = [], tempTime = [];
//     for (i = 0; i < selectedValue; i++) {
//         tempNum[i] = totNum[i];
//         tempTime[i] = newTime[i];
//     }

//     if (checkbox_Fri.checked) {

//         if (checkBoxReach.checked) {
//             reactChart.destroy();
//             checkBoxReach.checked = false;
//         }
//         if (checkBoxReact.checked) {
//             reachChart.destroy();
//             checkBoxReact.checked = false;
//         }

//         var ctx = document.getElementById(chartid).getContext('2d');
//         allFriendChart = new Chart(ctx, {
//             type: 'line',
//             data: {
//                 labels: tempTime,
//                 datasets: [
//                     {
//                         label: 'Number of react',
//                         fill: true,
//                         data: totNum,
//                         backgroundColor: 'rgba(107,142,35,5)',
//                         borderColor: 'rgba(85,107,47,1)',
//                         borderWidth: 3
//                     }]
//             },
//             options: {
//                 scales: {
//                     xAxes: [{
//                         gridLines: {
//                             color: 'rgba(255, 255, 255,0.5)',
//                             lineWidth: 2
//                         }
//                     }],
//                     yAxes: [{
//                         gridLines: {
//                             color: 'rgba(255, 255, 255,0.5)',
//                             lineWidth: 2
//                         },
//                         ticks: {
//                             beginAtZero: true,
//                         }
//                     }]
//                 }
//             }
//         });


//     }
//     else if (!checkbox_Fri.checked) {
//         allFriendChart.destroy();

//     }
// }

// var likechart, reactchart;
// function plot(chartid,newdata,newlabel){
//     var checkbox = document.getElementById("togLBtn");



//     if(checkbox.checked){
//         var ctx = document.getElementById(chartid).getContext('2d');
//         likechart = new Chart(ctx, {
//             type: 'bar',
//             data: {
//                 labels: newlabel,
//                 datasets: [{
//                     label: 'Number of Likes',
//                     data: newdata,
//                     backgroundColor: [
//                         'rgba(255, 99, 132, 0.2)'
//                         // 'rgba(54, 162, 235, 0.2)',
//                         // 'rgba(255, 206, 86, 0.2)',
//                         // 'rgba(75, 192, 192, 0.2)',
//                         // 'rgba(153, 102, 255, 0.2)',
//                         // 'rgba(255, 159, 64, 0.2)'
//                     ],
//                     // borderColor: [
//                     //     'rgba(255,99,132,1)',
//                     //     'rgba(54, 162, 235, 1)',
//                     //     'rgba(255, 206, 86, 1)',
//                     //     'rgba(75, 192, 192, 1)',
//                     //     'rgba(153, 102, 255, 1)',
//                     //     'rgba(255, 159, 64, 1)'
//                     // ],
//                     borderWidth: 0.5
//                 },{
//                     data: newdata,
//                     type:'line'
//                 }]
//             },
//             options: {
//             responsive: true,
//                 scales: {
//                     yAxes: [{
//                         ticks: {
//                             beginAtZero:true
//                         }
//                     }]
//                 }
//             }
//         });
//     } else if (!checkbox.checked) {
//         likechart.destroy();
//     }
// }


// function plotReaction(chartid,newLikes,newLove,newHaha,newWow,newSad,newAngry){
//     console.log(newAngry[0]);
//     console.log(newLove[8]);
//     console.log(newAngry[2]);
//     // newAngry[3], newAngry[4], newAngry[5], newAngry[6]);
//     var checkbox = document.getElementById("togRBtn");
//     if(checkbox.checked){
//     var ctx = document.getElementById(chartid).getContext('2d');
//         reactchart = new Chart(ctx, {
//             type: 'polarArea',
//             data: {
//                 labels: ["likes", "love", "haha", "wow", "sad", "angry"],
//                 datasets: [{
//                     label: 'Number of Reaction',
//                     data: [newLikes, newLove, newHaha, newWow, newSad, newAngry],
//                     backgroundColor: [
//                         'rgba(255, 99, 132, 0.2)',
//                         'rgba(54, 162, 235, 0.2)',
//                         'rgba(255, 206, 86, 0.2)',
//                         'rgba(75, 192, 192, 0.2)',
//                         'rgba(153, 102, 255, 0.2)',
//                         'rgba(255, 159, 64, 0.2)'
//                     ],
//                     borderColor: [
//                         'rgba(255,99,132,1)',
//                         'rgba(54, 162, 235, 1)',
//                         'rgba(255, 206, 86, 1)',
//                         'rgba(75, 192, 192, 1)',
//                         'rgba(153, 102, 255, 1)',
//                         'rgba(255, 159, 64, 1)'
//                     ],
//                     borderWidth: 5
//                 }]
//             },
//             options: {
//                 scales: {
//                     yAxes: [{
//                         ticks: {
//                             beginAtZero:true
//                         }
//                     }]
//                 }
//             }
//         });
//     } else if (!checkbox.checked) {
//         reactchart.destroy();
//     }
// }



// function addData(newdata,newlabel) {
//     // var data = [5,10,50,100];
//     // linechart.data.labels.pop();
//     linechart.data.labels.push(newlabel);
//     linechart.data.datasets[0].data = newdata;
//     linechart.update();
// }