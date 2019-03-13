<div class="panel panel-default">
    <div class="panel-heading">
        <i class="fa fa fa-file-text-o"></i>&nbsp; <?php echo lang("invoice_statistics"); ?>
    </div>
    <div class="panel-body ">
        <div id="timesheet-statistics-flotchart" style="width: 100%; height: 300px;"></div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        var invoiceStatisticsFlotchart = function() {
            var invoices =<?php echo $invoices; ?>,
                    payments = <?php echo $payments; ?>,
                    dataset = [
                {label: "<?php echo lang('invoice');?>",
                    data: invoices,
                    color: "#f11630",
                    lines: {
                        show: true,
                        fill: 0.2
                    },
                    shadowSize: 0
                },
                {
                    data: invoices,
                    color: "#fff",
                    lines: {
                        show: false
                    },
                    points: {
                        show: true,
                        fill: true,
                        radius: 4,
                        fillColor: "rgba(210,210,220,1)",
                        lineWidth: 2
                    },
                    curvedLines: {
                        apply: false
                    },
                    shadowSize: 0
                },
                {
                    label: "<?php echo lang('payment');?>",
                    data: payments,
                    color: "#70d467",
                    lines: {
                        show: true,
                        fill: 0.2
                    },
                    shadowSize: 0
                }, {
                    data: payments,
                    color: "#fff",
                    lines: {
                        show: false
                    },
                    curvedLines: {
                        apply: false
                    },
                    points: {
                        show: true,
                        fill: true,
                        radius: 4,
                        fillColor: "#00B393",
                        lineWidth: 2
                    },
                    shadowSize: 0
                }
            ];

            $.plot("#timesheet-statistics-flotchart", dataset, {
                series: {
                    lines: {
                        show: true,
                        fill: 0.3
                    },
                    shadowSize: 0,
                    curvedLines: {
                        apply: true,
                        active: true
                    }
                },
                legend: {
                    show: true
                },
                yaxis: {
                    min: 0
                },
                xaxis: {
                    ticks: [[1, "Jan"], [2, "Feb"], [3, "Mar"], [4, "Apr"], [5, "May"], [6, "Jun"], [7, "Jul"], [8, "Aug"], [9, "Sep"], [10, "Oct"], [11, "Nov"], [12, "Dec"]]
                },
                grid: {
                    color: "#bbb",
                    hoverable: true,
                    borderWidth: 0,
                    backgroundColor: '#FFF'
                },
                tooltip: true,
                tooltipOpts: {
                    content: function(x, y, z) {
                        if (x) {
                            return "%s: " + toCurrency(z);
                        } else {
                            return  toCurrency(z);
                        }
                    },
                    defaultTheme: false
                }
            });

        };

        invoiceStatisticsFlotchart();
    });
</script>    

