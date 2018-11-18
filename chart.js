var likechart, reactchart;
function plot(chartid,newdata,newlabel){
    var checkbox = document.getElementById("togLBtn");
    if(checkbox.checked){
        var ctx = document.getElementById(chartid).getContext('2d');
        likechart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: newlabel,
                datasets: [{
                    label: 'Number of Likes',
                    data: newdata,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)'
                        // 'rgba(54, 162, 235, 0.2)',
                        // 'rgba(255, 206, 86, 0.2)',
                        // 'rgba(75, 192, 192, 0.2)',
                        // 'rgba(153, 102, 255, 0.2)',
                        // 'rgba(255, 159, 64, 0.2)'
                    ],
                    // borderColor: [
                    //     'rgba(255,99,132,1)',
                    //     'rgba(54, 162, 235, 1)',
                    //     'rgba(255, 206, 86, 1)',
                    //     'rgba(75, 192, 192, 1)',
                    //     'rgba(153, 102, 255, 1)',
                    //     'rgba(255, 159, 64, 1)'
                    // ],
                    borderWidth: 0.5
                },{
                    data: newdata,
                    type:'line'
                }]
            },
            options: {
            responsive: true,
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero:true
                        }
                    }]
                }
            }
        });
    } else if (!checkbox.checked) {
        likechart.destroy();
    }
}


function plotReaction(chartid,newLikes,newLove,newHaha,newWow,newSad,newAngry){
    var checkbox = document.getElementById("togRBtn");
    if(checkbox.checked){
    var ctx = document.getElementById(chartid).getContext('2d');
        reactchart = new Chart(ctx, {
            type: 'polarArea',
            data: {
                labels: ["likes", "love", "haha", "wow", "sad", "angry"],
                datasets: [{
                    label: 'Number of Reaction',
                    data: [newLikes, newLove, newHaha, newWow, newSad, newAngry],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(255, 159, 64, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255,99,132,1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)'
                    ],
                    borderWidth: 5
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero:true
                        }
                    }]
                }
            }
        });
    } else if (!checkbox.checked) {
        reactchart.destroy();
    }
}



function addData(newdata,newlabel) {
    // var data = [5,10,50,100];
    // linechart.data.labels.pop();
    linechart.data.labels.push(newlabel);
    linechart.data.datasets[0].data = newdata;
    linechart.update();
}