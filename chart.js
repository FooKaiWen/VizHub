var selected_value = 50;
var selector = document.getElementById("selected");

selector.addEventListener('change',function(){
    if(selected_value != selector[selector.selectedIndex].value){
        selected_value = selector[selector.selectedIndex].value
    }
});

function hideselect(){
    var selector = document.getElementById("selected");
    var info = document.getElementById("info");
    selector.style.display = 'none';
    info.style.display = 'none';
}

function showselect(){
    var selector = document.getElementById("selected");
    var info = document.getElementById("info");
    selector.style.display = 'initial';
    info.style.display = 'inherit';
}

var checkbox_All = document.getElementById("togAllBtn");
var checkbox_Tot = document.getElementById("togTotBtn");
var checkbox_Fri = document.getElementById("togFriBtn");

checkbox_All.addEventListener('change',function(){
    if(this.checked){
        showselect();
    } else if(!this.checked){
        hideselect();
    }
});

checkbox_Tot.addEventListener('change',function(){
    if(this.checked){
        showselect();
    } else if(!this.checked){
        hideselect();
    }
});

checkbox_Fri.addEventListener('change',function(){
    if(this.checked){
        showselect();
    } else if(!this.checked){
        hideselect();
    }
});

Chart.defaults.global.defaultFontColor = 'white';

var allReactChart;
<<<<<<< HEAD
function plotAll(chartid, newLikes, newComment, newShare, newTime) {
    var checkbox = document.getElementById("togAllBtn")
    console.log(newComment[2]);
=======
function plotAll(chartid, newLikes, newLove, newHaha, newWow, newSad, newAngry, newTime) {
>>>>>>> 28fb58b9ba33ae6d42ec70f5d81110df3900737a

    var temp = [];
    for(i=0;i<selected_value;i++){
        temp[i] = newLikes[i];
        console.log(temp[i]);
    }
    var checkbox_All = document.getElementById("togAllBtn");
    var checkbox_Tot = document.getElementById("togTotBtn");
    var checkbox_Fri = document.getElementById("togFriBtn");

    if (checkbox_All.checked) {

        if(checkbox_Tot.checked){
            totalChart.destroy();
            checkbox_Tot.checked = false;
        }
        if(checkbox_Fri.checked){
            allFriendChart.destroy();
            checkbox_Fri.checked = false;
        }
        
        var ctx = document.getElementById(chartid).getContext('2d');
        allReactChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: newTime,
                datasets: [
                    {
                        label: 'Likes',
                        fill: false,
                        //lineTension: 0.5,
                        data: newLikes,
                        backgroundColor: 'rgba(100,149,237, 0.2)',
                        borderColor: 'rgba(100,149,237, 1)',
                        borderWidth: 3
                    },
                    {
                        label: 'Comment',
                        data: newComment,
                        backgroundColor: 'rgba(255,140,0, 0.2)',
                        borderColor: 'rgba(255,140,0, 1)',
                        borderWidth: 3
                    },
                    {
                        label: 'Share',
                        data: newShare,
                        backgroundColor: 'rgba(178,34,34, 0.8)',
                        borderColor: 'rgba(178,34,34,1)',
                        borderWidth: 3
                    }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });

    }
    else if (!checkbox.checked) {
        allReactChart.destroy();

    }

}

var topChart;
function plotTop(chartid, newLikes, newLove, newHaha, newWow, newSad, newAngry, newTime) {
    var selectValue = document.getElementById("topReactId");
    console.log(selectValue.value);

    if (selectValue.value == 5) {
        var ctx = document.getElementById(chartid).getContext('2d');
        topChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: newTime,
                datasets: [
                    {
                        label: 'Likes',
                        fill: false,
                        //lineTension: 0.5,
                        data: newLikes,
                        backgroundColor: 'rgba(255, 99, 132, 0.8)',
                        borderColor: 'rgba(255,99,132,1)',
                        borderWidth: 3
                    },
                    {
                        label: 'Love',
                        data: newLove,
                        backgroundColor: 'rgba(54, 162, 235, 0.8)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 3
                    },
                    {
                        label: 'Haha',
                        data: newHaha,
                        backgroundColor: 'rgba(255, 206, 86, 0.8)',
                        borderColor: 'rgba(255, 206, 86, 1)',
                        borderWidth: 3
                    },
                    {
                        label: 'Wow',
                        data: newWow,
                        backgroundColor: 'rgba(75, 192, 192, 0.8)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 3
                    },
                    {
                        label: 'Sad',
                        data: newSad,
                        backgroundColor: 'rgba(153, 102, 255, 0.8)',
                        borderColor: 'rgba(153, 102, 255, 1)',
                        borderWidth: 3

                    },
                    {
                        label: 'Angry',
                        data: newAngry,
                        backgroundColor: 'rgba(255, 159, 64, 0.8)',
                        borderColor: 'rgba(255, 159, 64, 1)',
                        borderWidth: 3
                    }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });

    }
    else if (!checkbox_All.checked) {
        allReactChart.destroy();

    }
}

var totalChart;
<<<<<<< HEAD
function plotTotal(chartid, newLove, newHaha, newWow, newSad, newAngry, newTime) {
    // var totLikes=0, totLove=0, totHaha=0, totWow=0, totSad=0, totAngry=0;
    console.log("he cb");
    var checkbox = document.getElementById("togTotBtn");
    
=======
function plotTotal(chartid, newLikes, newLove, newHaha, newWow, newSad, newAngry) {
    var totLikes=0, totLove=0, totHaha=0, totWow=0, totSad=0, totAngry=0;

    var checkbox_All = document.getElementById("togAllBtn");
    var checkbox_Tot = document.getElementById("togTotBtn");
    var checkbox_Fri = document.getElementById("togFriBtn");
>>>>>>> 28fb58b9ba33ae6d42ec70f5d81110df3900737a

    // for (var i = 0; i < 15; i++) {
    //     totLikes += newLikes[i];
    //     totLove += newLove[i];
    //     totHaha += newHaha[i];
    //     totWow += newWow[i];
    //     totSad += newSad[i];
    //     totAngry += newAngry[i];
    // }

    if (checkbox.checked) {

        if(checkbox_All.checked){
            allReactChart.destroy();
            checkbox_All.checked = false;
        }
        if(checkbox_Fri.checked){
            allFriendChart.destroy();
            checkbox_Fri.checked = false;
        }
        
        var ctx = document.getElementById(chartid).getContext('2d');
        totalChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: newTime,
                datasets: [{
                    label: 'Love',
                    fill: true,
                    backgroundColor: 'rgba(128,0,0, 0.8)',
                    borderColor: 'rgba(128,0,0, 1)',
                    data: newLove,
                }, {

                    label: 'Haha',
                    backgroundColor: 'rgba(255,165,0, 0.8)',
                    borderColor: 'rgba(255,165,0, 1)',
                    data: newHaha,
                    fill: true,

                }, {

                    label: 'Wow',
                    backgroundColor: 'rgba(46,139,87, 0.8)',
                    borderColor: 'rgba(46,139,87, 1)',
                    data: newWow,
                    fill: true,
                }, {

                    label: 'Sad',
                    backgroundColor: 'rgba(153,50,204, 0.8)',
                    borderColor: 'rgba(153,50,204, 0.8)',
                    data: newSad,
                    fill: true,
                }, {

                    label: 'Angry',
                    backgroundColor: 'rgba(112,128,144, 0.8)',
                    borderColor: 'rgba(112,128,144, 1.0)',
                    data: newAngry,
                    fill: true,
                }]
            },
            options: {
                scales: {
                    xAxes: [{
                        stacked: true
                    }],

                    yAxes: [{
                        stacked: true
                    }],
                    ticks: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
    else if (!checkbox_Tot.checked) {
        totalChart.destroy();
    }

}

var allFriendChart;
var friendNum;
function plotFriend(chartid, newLikes, newLove, newHaha, newWow, newSad, newAngry, newTime, newFriend) {
<<<<<<< HEAD
    var totNum = [];
    var checkbox = document.getElementById("togFriBtn");
=======
    var  totNum = [];
>>>>>>> 28fb58b9ba33ae6d42ec70f5d81110df3900737a

    var checkbox_All = document.getElementById("togAllBtn");
    var checkbox_Tot = document.getElementById("togTotBtn");
    var checkbox_Fri = document.getElementById("togFriBtn");

    for (var i = 0; i < 28; i++) {
        totNum[i] = 0;
        totNum[i] += newLikes[i];
        totNum[i] += newLove[i];
        totNum[i] += newHaha[i];
        totNum[i] += newWow[i];
        totNum[i] += newSad[i];
        totNum[i] += newAngry[i];
<<<<<<< HEAD
        totNum[i] /= newFriend; //

        if (totNum[i] < 0.2) {
            totNum[i] *= -1;
        }
=======
        totNum[i] /= newFriend;
        totNum[i] *= 100;
        // if(totNum[i] < 5){
        //     totNum[i] *= -1;
        // }
>>>>>>> 28fb58b9ba33ae6d42ec70f5d81110df3900737a
        console.log(newLikes[i]);
        console.log(totNum[i]);
    }

    if (checkbox_Fri.checked) {

        if(checkbox_All.checked){
            allReactChart.destroy();
            checkbox_All.checked = false;
        }
        if(checkbox_Tot.checked){
            totalChart.destroy();
            checkbox_Tot.checked = false;
        }
        
        var ctx = document.getElementById(chartid).getContext('2d');
        allFriendChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: newTime,
                datasets: [
                    {
                        label: 'Number of react',
                        fill: true,
                        data: totNum,
                        backgroundColor: 'rgba(85,107,47,1)',
                        borderColor: 'rgba(85,107,47,5)',
                        borderWidth: 3
                    }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            min:0.2,
                        }
                    }]
                }
            }
        });
<<<<<<< HEAD


=======
       
>>>>>>> 28fb58b9ba33ae6d42ec70f5d81110df3900737a
    }
    else if (!checkbox_Fri.checked) {
        allFriendChart.destroy();

    }
}

function friendNumber() {
    return friendNum;
}




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