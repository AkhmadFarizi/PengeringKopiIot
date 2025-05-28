<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengering Kopi</title>
    <link rel="icon" type="image/png" href="	https://upload.wikimedia.org/wikipedia/id/9/9a/Logo_PPNS.png">
    <link rel="apple-touch-icon" sizes="180x180" href="	https://upload.wikimedia.org/wikipedia/id/9/9a/Logo_PPNS.png">
    <link rel="icon" type="image/png" sizes="32x32"
        href="	https://upload.wikimedia.org/wikipedia/id/9/9a/Logo_PPNS.png">
    <link rel="icon" type="image/png" sizes="16x16"
        href="	https://upload.wikimedia.org/wikipedia/id/9/9a/Logo_PPNS.png">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

    <!-- Bootstrap 5 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">


    <!-- Leaflet (Maps) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</head>

<body>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Inter Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        .card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .card-header {
            font-weight: bold;
            background-color: #f8f9fa;
        }

        .badge {
            font-size: 0.9rem;
            padding: 0.35em 0.65em;
            font-weight: 500;
        }

        .badge-success {
            background-color: #28a745;
        }

        .badge-danger {
            background-color: #dc3545;
        }

        .badge-secondary {
            background-color: #6c757d;
        }

        #sensorChart,
        #tempGauge,
        #rpmGauge,
        #currentGauge {
            width: 100%;
        }

        .navbar {
            background-color: #ffffff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .navbar-brand {
            font-weight: 600;
            font-size: 1.1rem;
            color: #1f2937;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav-link {
            padding-left: 5rem;
            padding-right: 5rem;
            position: relative;
            color: #7b7f84;
        }

        .nav-link.actives::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: -0.3rem;
            width: 100%;
            height: 2px;
            background-color: #2563eb;
        }

        .dropdown-menu {
            border-radius: 0.5rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
            border: none;
        }

        @media (max-width: 991.98px) {
            .navbar-collapse {
                margin-top: 1rem;
                text-align: center;
            }

            .nav-link {
                padding-top: 0.5rem;
                padding-bottom: 0.5rem;
                display: block;
            }

            .mobile-user-menu {
                margin-top: 1rem;
                padding-top: 1rem;
                border-top: 1px solid #e5e7eb;
            }

            .mobile-user-menu .nav-link {
                padding: 0.5rem 0;
            }

            .mobile-user-menu .btn-link {
                color: #1f2937;
                font-weight: 500;
                text-decoration: none;
                padding: 0.5rem 0;
                display: block;
                width: 100%;
                text-align: center;
            }
        }
    </style>

    <nav class="navbar navbar-expand-md">
        <div class="container">
            <!-- Logo -->
            <a class="navbar-brand" href="/">
                <img src="https://upload.wikimedia.org/wikipedia/id/9/9a/Logo_PPNS.png" alt="Logo" height="40" />
                <span>Monitoring Pengering Kopi Sistem</span>
            </a>



            <!-- Menu -->
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav align-items-center w-100 justify-content-end">
                    @guest
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('/') ? 'actives' : '' }}" href="/">Dashboard</a>
                        </li>
                    @endguest

                </ul>
            </div>
        </div>
    </nav>


    <div class="container">
        <h3 class="mt-2 text-center">Sensor Log Monitoring</h3>

        <!-- Gauges Row -->
        <div class="row mb-4">
            <!-- Temperature Gauge -->
            <div class="col-xl-3 col-md-6 col-sm-12 mt-2">
                <div class="card shadow-lg bg-white rounded-4 border-0 h-100">
                    <div class="card-body text-center">
                        <h5 class="font-weight-bold mb-3">Suhu (°C)</h5>
                        <div style="position: relative; height: 200px;">
                            <canvas id="tempGauge"></canvas>
                            <div id="tempValue"
                                style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center;">
                                <span style="font-size: 32px; font-weight: bold;">0.0</span>
                                <span style="font-size: 16px;">°C</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RPM Gauge -->
            <div class="col-xl-3 col-md-6 col-sm-12 mt-2">
                <div class="card shadow-lg bg-white rounded-4 border-0 h-100">
                    <div class="card-body text-center">
                        <h5 class="font-weight-bold mb-3">RPM</h5>
                        <div id="rpmGauge" style="height: 200px;"></div>
                    </div>
                </div>
            </div>

            <!-- Current Gauge -->
            <div class="col-xl-3 col-md-6 col-sm-12 mt-2">
                <div class="card shadow-lg bg-white rounded-4 border-0 h-100">
                    <div class="card-body text-center">
                        <h5 class="font-weight-bold mb-3">Arus (A)</h5>
                        <div style="position: relative; height: 200px;">
                            <canvas id="currentGauge"></canvas>
                            <div id="currentValue"
                                style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center;">
                                <span style="font-size: 32px; font-weight: bold;">0.0</span>
                                <span style="font-size: 16px;">A</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-xl-3 col-md-6 col-sm-12 mt-2">
                <div class="card shadow-lg bg-white rounded-4 border-0 h-100">
                    <div class="card-body text-center">
                        <h4 class="fw-bold mb-4">Status Relay</h4>

                        <div class="d-flex flex-column align-items-start px-4">
                            <!-- Fan Status -->
                            <div class="d-flex align-items-center justify-content-between w-100 mb-4">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-fan fa-2x me-3 text-primary"></i>
                                    <span class="fw-semibold fs-5">Fan</span>
                                </div>
                                <span id="fanStatus" class="badge rounded-pill bg-secondary px-4 py-2 fs-6">OFF</span>
                            </div>

                            <!-- Heater Status -->
                            <div class="d-flex align-items-center justify-content-between w-100">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-fire fa-2x me-3 text-danger"></i>
                                    <span class="fw-semibold fs-5">Heater</span>
                                </div>
                                <span id="heaterStatus"
                                    class="badge rounded-pill bg-secondary px-4 py-2 fs-6">OFF</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>



        </div>



        <div class="row mt-4">
            <!-- Temperature History -->
            <div class="col-12 mb-4">
                <div class="card shadow-lg bg-white rounded-4 border-0 h-100">
                    <div class="card-body text-center">
                        <h5 class="font-weight-bold mb-3">Riwayat Suhu</h5>
                        <div id="tempChart" style="height: 300px;"></div>
                    </div>
                </div>
            </div>

            <!-- RPM History -->
            <div class="col-md-6 mb-4">
                <div class="card shadow-lg bg-white rounded-4 border-0 h-100">
                    <div class="card-body text-center">
                        <h5 class="font-weight-bold mb-3">Riwayat RPM</h5>
                        <div id="rpmChart" style="height: 300px;"></div>
                    </div>
                </div>
            </div>

            <!-- Current History -->
            <div class="col-md-6 mb-4">
                <div class="card shadow-lg bg-white rounded-4 border-0 h-100">
                    <div class="card-body text-center">
                        <h5 class="font-weight-bold mb-3">Riwayat Arus</h5>
                        <div id="currentChart" style="height: 300px;"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>



    <!-- Highcharts -->
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/highcharts-more.js"></script>
    <script src="https://code.highcharts.com/modules/solid-gauge.js"></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inisialisasi semua chart dan gauge
            initCharts();

            // Ambil data pertama kali
            fetchData();

            // Polling data setiap 5 detik
            setInterval(fetchData, 5000);
        });
        Highcharts.setOptions({
            time: {
                useUTC: false
            }
        });



        function initCharts() {
            // 1. Inisialisasi Chart.js Gauge untuk Suhu
            window.tempGauge = new Chart(
                document.getElementById('tempGauge').getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        datasets: [{
                            data: [0, 100],
                            backgroundColor: ['#55BF3B', '#eeeeee'],
                            borderWidth: 0,
                            circumference: 270,
                            rotation: 225,
                            cutout: '75%'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                enabled: false
                            }
                        }
                    }
                }
            );

            // 2. Inisialisasi Highcharts Gauge untuk RPM
            window.rpmGauge = Highcharts.chart('rpmGauge', {
                chart: {
                    type: 'gauge',
                    plotBackgroundColor: null,
                    plotBackgroundImage: null,
                    plotBorderWidth: 0,
                    plotShadow: false
                },
                title: null,
                pane: {
                    startAngle: -150,
                    endAngle: 150,
                    background: [{
                        backgroundColor: {
                            linearGradient: {
                                x1: 0,
                                y1: 0,
                                x2: 0,
                                y2: 1
                            },
                            stops: [
                                [0, '#FFF'],
                                [1, '#333']
                            ]
                        },
                        borderWidth: 0,
                        outerRadius: '109%'
                    }, {
                        backgroundColor: {
                            linearGradient: {
                                x1: 0,
                                y1: 0,
                                x2: 0,
                                y2: 1
                            },
                            stops: [
                                [0, '#333'],
                                [1, '#FFF']
                            ]
                        },
                        borderWidth: 1,
                        outerRadius: '107%'
                    }, {
                        // default background
                    }, {
                        backgroundColor: '#DDD',
                        borderWidth: 0,
                        outerRadius: '105%',
                        innerRadius: '103%'
                    }]
                },
                yAxis: {
                    min: 0,
                    max: 5000,
                    minorTickInterval: 'auto',
                    minorTickWidth: 1,
                    minorTickLength: 10,
                    minorTickPosition: 'inside',
                    minorTickColor: '#666',
                    tickPixelInterval: 30,
                    tickWidth: 2,
                    tickPosition: 'inside',
                    tickLength: 10,
                    tickColor: '#666',
                    labels: {
                        step: 2,
                        rotation: 'auto'
                    },
                    title: {
                        text: 'RPM'
                    },
                    plotBands: [{
                        from: 0,
                        to: 2000,
                        color: '#55BF3B' // hijau
                    }, {
                        from: 2000,
                        to: 4000,
                        color: '#DDDF0D' // kuning
                    }, {
                        from: 4000,
                        to: 5000,
                        color: '#DF5353' // merah
                    }]
                },
                series: [{
                    name: 'RPM',
                    data: [0],
                    tooltip: {
                        valueSuffix: ' RPM'
                    }
                }]
            });

            // 3. Inisialisasi Chart.js Gauge untuk Arus
            window.currentGauge = new Chart(
                document.getElementById('currentGauge').getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        datasets: [{
                            data: [0, 10],
                            backgroundColor: ['#55BF3B', '#eeeeee'],
                            borderWidth: 0,
                            circumference: 270,
                            rotation: 225,
                            cutout: '75%'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                enabled: false
                            }
                        }
                    }
                }
            );

            // 4. Inisialisasi Highcharts untuk Chart Riwayat
            window.tempChart = Highcharts.chart('tempChart', createChartConfig('Suhu', '°C', '#55BF3B'));
            window.rpmChart = Highcharts.chart('rpmChart', createChartConfig('RPM', '', '#DDDF0D'));
            window.currentChart = Highcharts.chart('currentChart', createChartConfig('Arus', 'mA', '#DF5353'));
        }

        function createChartConfig(title, unit, color) {
            return {
                chart: {
                    type: 'spline',
                    height: 380,
                    zoomType: 'x'
                },
                title: {
                    text: title
                },
                xAxis: {
                    type: 'datetime',
                    labels: {
                        format: '{value:%d-%m - %H:%M}',
                        rotation: -45
                    }
                },
                yAxis: {
                    title: {
                        text: `${title}${unit ? ` (${unit})` : ''}`
                    }
                },
                series: [{
                    name: title,
                    data: [],
                    color: color
                }],
                tooltip: {
                    xDateFormat: '%Y-%m-%d %H:%M:%S',
                    shared: true,
                    crosshairs: true
                },
                credits: {
                    enabled: false
                },
                exporting: {
                    enabled: true,
                    buttons: {
                        contextButton: {
                            menuItems: ['downloadPNG', 'downloadJPEG', 'downloadPDF', 'downloadSVG']
                        }
                    }
                }
            };
        }

        function fetchData() {
            // Ambil data terbaru
            fetch('/api/sensor-logs/latest')
                .then(response => response.json())
                .then(data => {
                    console.log('Latest data:', data); // Debug log
                    // Update gauge suhu (Chart.js)
                    updateChartJSGauge(window.tempGauge, 'tempValue', '°C', data.suhu, 100, [{
                            limit: 30,
                            color: '#55BF3B'
                        },
                        {
                            limit: 70,
                            color: '#DDDF0D'
                        },
                        {
                            color: '#DF5353'
                        }
                    ]);

                    // Update gauge RPM (Highcharts)
                    if (window.rpmGauge && window.rpmGauge.series) {
                        window.rpmGauge.series[0].points[0].update(data.rpm);
                    }

                    // Update gauge arus (Chart.js)
                    updateChartJSGauge(window.currentGauge, 'currentValue', 'mA', data.arus, 1000, [{
                            limit: 3,
                            color: '#55BF3B'
                        },
                        {
                            limit: 7,
                            color: '#DDDF0D'
                        },
                        {
                            color: '#DF5353'
                        }
                    ]);

                    // Update status relay
                    updateRelayStatus(data.relayFan, data.relayHeater);
                });

            // Ambil data riwayat untuk chart
            fetch('/api/sensor-logs')
                .then(res => res.json())
                .then(data => {
                    window.tempChart.series[0].setData(data.map(i => [i.timestamp, i.suhu]));
                    window.rpmChart.series[0].setData(data.map(i => [i.timestamp, i.rpm]));
                    window.currentChart.series[0].setData(data.map(i => [i.timestamp, i.arus]));
                });
        }

        // Helper function untuk update Chart.js gauge
        function updateChartJSGauge(gauge, valueId, unit, value, max, colorStops) {
            value = Math.max(0, Math.min(max, value));

            // Update chart data
            gauge.data.datasets[0].data = [value, max - value];
            gauge.data.datasets[0].backgroundColor = [
                getColorForValue(value, colorStops),
                '#eeeeee'
            ];
            gauge.update();

            // Update nilai di tengah
            document.getElementById(valueId).innerHTML = `
            <span style="font-size: 32px; font-weight: bold;">${value % 1 === 0 ? value : value.toFixed(1)}</span>
            <span style="font-size: 16px;">${unit}</span>
        `;
        }

        // Helper function untuk menentukan warna berdasarkan nilai
        function getColorForValue(value, stops) {
            for (let i = 0; i < stops.length; i++) {
                if (i === stops.length - 1 || value <= stops[i].limit) {
                    return stops[i].color;
                }
            }
            return '#DF5353';
        }

        // Helper function untuk update status relay
        function updateRelayStatus(fanStatus, heaterStatus) {
            console.log('Updating relay status:', fanStatus, heaterStatus); // Debug log

            const fanElement = document.getElementById('fanStatus');
            const heaterElement = document.getElementById('heaterStatus');

            if (fanElement) {
                fanElement.textContent = fanStatus;
                fanElement.className = fanStatus === 'ON' ? 'badge badge-success' : 'badge badge-secondary';
            } else {
                console.error('Element fanStatus not found');
            }

            if (heaterElement) {
                heaterElement.textContent = heaterStatus;
                heaterElement.className = heaterStatus === 'ON' ? 'badge badge-danger' : 'badge badge-secondary';
            } else {
                console.error('Element heaterStatus not found');
            }
        }
    </script>
</body>

</html>
