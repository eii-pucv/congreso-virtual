<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

        <!-- Bootstrap 4 minified CSS -->
        <link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap4.min.css') }}" >

        <!-- Font Awesome minified CSS -->
        <link rel="stylesheet" type="text/css" href="{{ asset('fontawesome/css/all.min.css') }}">

        {{-- make sure you are using http, and not https --}}
        <script type="text/javascript" src="https://www.google.com/jsapi"></script>

        <!-- Load d3.js -->
        <script src="{{ asset('js/d3.v3.min.js') }}"></script>

        <!-- Load d3-cloud -->
        <script src="{{ asset('js/d3.layout.cloud.js') }}"></script>

        <!-- Load WordCloud2.js (not used)-->
        <!--<script src="{{ asset('js/wordcloud2.js') }}"></script>-->

        <script type="text/javascript">
            function init() {
                google.load('visualization', '1.1', {
                    packages: [
                        'corechart',
                        'geochart',
                        'bar'
                    ],
                    callback: drawCharts
                });

                var chartsRun = 1;
                setInterval(function() {
                    if(chartsRun) {
                        chartsRun = 0;
                        google.setOnLoadCallback(drawCharts);
                    }
                }, 30000);
            }

            function drawCharts() {
                function generatePieChart(data, idElementToSelect) {
                    var pieChart = new google.visualization.PieChart(document.getElementById(idElementToSelect));
                    pieChart.draw(
                        google.visualization.arrayToDataTable(data),
                        {
                            is3D: true,
                            backgroundColor: 'transparent',
                            chartArea: {
                                width: '100%',
                                height: '90%'
                            },
                            width: 350,
                            height: 200
                        }
                    );
                }

                generatePieChart([
                    ['Voto', 'Cantidad'],
                    ['A favor', {{ $project->votos_a_favor }} ],
                    ['En contra', {{ $project->votos_en_contra }} ],
                    ['Abstención', {{ $project->abstencion }} ]
                ], 'votacion-general-pie-chart');

                generatePieChart([
                    ['Género', 'Cantidad'],
                    ['Masculino', {{ $usersParticipantsGenderCount->male }} ],
                    ['Femenino', {{ $usersParticipantsGenderCount->female }} ],
                    ['Otro', {{ $usersParticipantsGenderCount->other }} ],
                    ['No responde', {{ $usersParticipantsGenderCount->not_answer }} ]
                ], 'participantes-genero-pie-chart');

                generatePieChart([
                    ['Rango de edad', 'Cantidad'],
                    ['10-19', {{ $usersParticipantsAgeRangeCount->_10_19 }} ],
                    ['20-29', {{ $usersParticipantsAgeRangeCount->_20_29 }} ],
                    ['30-39', {{ $usersParticipantsAgeRangeCount->_30_39 }} ],
                    ['40-49', {{ $usersParticipantsAgeRangeCount->_40_49 }} ],
                    ['50-59', {{ $usersParticipantsAgeRangeCount->_50_59 }} ],
                    ['60-69', {{ $usersParticipantsAgeRangeCount->_60_69 }} ],
                    ['70-79', {{ $usersParticipantsAgeRangeCount->_70_79 }} ],
                    ['80-89', {{ $usersParticipantsAgeRangeCount->_80_89 }} ]
                ], 'participantes-edad-pie-chart');


                var projectIdeas = @json($project->ideas);
                projectIdeas.forEach(function (idea) {
                    generatePieChart([
                        ['Voto', 'Cantidad'],
                        ['A favor', idea.votos_a_favor ],
                        ['En contra', idea.votos_en_contra ],
                        ['Abstención', idea.abstencion ]
                    ], 'votacion-idea-' + idea.id + '-pie-chart');
                });

                var participantesEdadGeneroColumnChart = new google.visualization.ColumnChart(document.getElementById('participantes-edad-genero-column-chart'));
                participantesEdadGeneroColumnChart.draw(
                    google.visualization.arrayToDataTable([
                        ['Rango de edad', 'Masculino', 'Femenino', 'Otro', 'No responde'],
                        ['10-19', {{ implode(',', array_values((array) $usersParticipantsAgeRangeGenderCount->_10_19)) }} ],
                        ['20-29', {{ implode(',', array_values((array) $usersParticipantsAgeRangeGenderCount->_20_29)) }} ],
                        ['30-39', {{ implode(',', array_values((array) $usersParticipantsAgeRangeGenderCount->_30_39)) }} ],
                        ['40-49', {{ implode(',', array_values((array) $usersParticipantsAgeRangeGenderCount->_40_49)) }} ],
                        ['50-59', {{ implode(',', array_values((array) $usersParticipantsAgeRangeGenderCount->_50_59)) }} ],
                        ['60-69', {{ implode(',', array_values((array) $usersParticipantsAgeRangeGenderCount->_60_69)) }} ],
                        ['70-79', {{ implode(',', array_values((array) $usersParticipantsAgeRangeGenderCount->_70_79)) }} ],
                        ['80-89', {{ implode(',', array_values((array) $usersParticipantsAgeRangeGenderCount->_80_89)) }} ]
                    ]),
                    {
                        vAxis: {
                            format: 'decimal'
                        },
                        backgroundColor: 'transparent',
                        chartArea: {
                            width: '70%',
                            height: '80%',
                            left: 60
                        },
                        width: 650,
                        height: 250
                    }
                );

                var usersParticipantsProvincesCount = @json((array) $usersParticipantsProvincesCount);

                var barChartData = [['Region', 'Cantidad', { role: 'annotation' }]];
                usersParticipantsProvincesCount.forEach(function (usersParticipantsProvinceCount) {
                    barChartData.push([
                        usersParticipantsProvinceCount.name,
                        usersParticipantsProvinceCount.count,
                        usersParticipantsProvinceCount.count
                    ]);
                });

                var participantesRegionBarChart = new google.visualization.BarChart(document.getElementById('participantes-region-bar-chart'));
                participantesRegionBarChart.draw(
                    google.visualization.arrayToDataTable(barChartData),
                    {
                        backgroundColor: 'transparent',
                        legend: { position: 'none' },
                        chartArea: {
                            width: '40%',
                            height: '80%',
                            right: 120
                        },
                        width: 650,
                        height: 400
                    }
                );

                usersParticipantsProvincesCount.forEach(function (usersParticipantsProvinceCount) {
                    if(usersParticipantsProvinceCount.code === 'CL-NB') {
                        var nubleProvince = usersParticipantsProvinceCount;
                        usersParticipantsProvincesCount.forEach(function (usersParticipantsProvinceCount) {
                            if(usersParticipantsProvinceCount.code === 'CL-BI') {
                                usersParticipantsProvinceCount.count += nubleProvince.count;
                            }
                        });
                    }
                });

                var geoChartData = [[ 'Región', 'Cantidad' ]];
                usersParticipantsProvincesCount.forEach(function (usersParticipantsProvinceCount) {
                    geoChartData.push([
                        {
                            f: usersParticipantsProvinceCount.name,
                            v: usersParticipantsProvinceCount.code
                        },
                        usersParticipantsProvinceCount.count
                    ]);
                });
                var participantesRegionGeoChart = new google.visualization.GeoChart(document.getElementById('participantes-region-geo-chart'));
                participantesRegionGeoChart.draw(
                    google.visualization.arrayToDataTable(geoChartData),
                    {
                        region: 'CL',
                        resolution: 'provinces',
                        colorAxis: {
                            colors: 'blue'
                        },
                        backgroundColor: 'transparent',
                        width: 550,
                        height: 400,
                    }
                );

                var wordCloudData = @json($wordCloudData);
                if(wordCloudData) {
                    var parsedWords = wordCloudData.map(function (word) {
                        return {
                            text: word.word,
                            size: 10 + Math.random() * (word.freq / 2)
                        };
                    });
                    generateWordCloud(parsedWords, '#word-cloud');
                }

                function generateWordCloud(words, selector) {
                    var fill = d3.scale.category10();
                    var width = 900;
                    var height = 300;

                    var svg = d3.select(selector).append("svg")
                        .attr("width", width)
                        .attr("height", height)
                        .append("g")
                        .attr("transform", "translate(" + width/2 + "," + height/2 + ")");

                    d3.layout.cloud().size([width, height])
                        .words(words)
                        .padding(3)
                        .rotate(0)
                        .font("Impact")
                        .fontSize(function(d) { return d.size; })
                        .on("end", draw)
                        .start();

                    function draw(words) {
                        var cloud = svg.selectAll("g text")
                            .data(words, function(d) { return d.text; })

                        cloud.enter()
                            .append("text")
                            .style("font-family", "Impact")
                            .style("fill", function(d, i) { return fill(i); })
                            .attr("text-anchor", "middle")
                            .attr('font-size', 1)
                            .text(function(d) { return d.text; });

                        cloud
                            .style("font-size", function(d) { return d.size + "px"; })
                            .attr("transform", function(d) {
                                return "translate(" + [d.x, d.y] + ")";
                            });
                    }
                }
            }
        </script>
    </head>
    <body onload="init()">
        <div class="page-header text-center">
            <h2>
                Reporte Generado por {{ config('app.name') }}<br>
                para el Proyecto de Ley:
            </h2>
            <h3>
                {{ $project->titulo }}<br>
                <small class="text-secondary">N° de Boletín: {{ $project->boletin }}</small>
            </h3>
        </div>
        <hr>
        <div>
            <h4 class="mt-5 mb-3">Información General del Proyecto de Ley</h4>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th style="width: 30%;">Ítem</th>
                        <th>Detalle</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th>ID</th>
                        <td>{{ $project->id }}</td>
                    </tr>
                    <tr>
                        <th>Título</th>
                        <td>{{ $project->titulo }}</td>
                    </tr>
                    <tr>
                        <th>N° Boletín</th>
                        <td>{{ $project->boletin }}</td>
                    </tr>
                    <tr>
                        <th>Postulante(s)</th>
                        <td>{{ $project->postulante }}</td>
                    </tr>
                    <tr>
                        <th>Estado</th>
                        <td>{{ $project->estado }}</td>
                    </tr>
                    <tr>
                        <th>Resumen</th>
                        <td>{{ $project->resumen }}</td>
                    </tr>
                    <tr>
                        <th>Enlace</th>
                        <td>
                            <a href="{{ secure_url(env('APP_CLIENT_URL')) . '/project/' . $project->id }}">
                                {{ secure_url(env('APP_CLIENT_URL')) . '/project/' . $project->id }}
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div>
            <h4 class="mt-5 mb-3">Información General de Participación</h4>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th style="width: 30%;">Ítem</th>
                        <th>Detalle</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th>Etapa actual</th>
                        @if($project->etapa == 1)
                            <td>Votación en General</td>
                        @elseif($project->etapa == 2)
                            <td>Votación en Particular</td>
                        @elseif($project->etapa == 3)
                            <td>Votación Cerrada</td>
                        @endif
                    </tr>
                    <tr>
                        <th>Fecha de inicio de las votaciones</th>
                        <td>{{ $fechaInicio->formatLocalized('%d %b %Y, %H:%M:%S') }} Hrs. (UTC)</td>
                    </tr>
                    <tr>
                        <th>Fecha de término de las votaciones</th>
                        <td>{{ $fechaTermino->formatLocalized('%d %b %Y, %H:%M:%S') }} Hrs. (UTC)</td>
                    </tr>
                    <tr>
                        <th>Cantidad de días de votación</th>
                        <td>{{ $fechaInicio->diffInDays($fechaTermino) }} día(s)</td>
                    </tr>
                    <tr>
                        <th>Cantidad de votos totales</th>
                        <td>{{ $project->votes()->count() }}</td>
                    </tr>
                    <tr>
                        <th>Cantidad de comentarios totales</th>
                        <td>{{ $project->comments()->count() }}</td>
                    </tr>
                    <tr>
                        <th>Cantidad de usuarios participantes</th>
                        <td>{{ $usersParticipantsOnProject ? count($usersParticipantsOnProject) : 0 }}</td>
                    </tr>
                    <tr>
                        <th>Cantidad de usuarios con al menos un tema de interés en común</th>
                        <td>{{ $usersWithProjectTerms ? count($usersWithProjectTerms) : 0 }}</td>
                    </tr>
                </tbody>
            </table>
            <div class="alert alert-info">
                <span><i class="fas fa-info-circle"></i> Un usuario se considera participante si éste realizó un voto o al menos un comentario en el proyecto de ley.</span>
            </div>
        </div>
        <div>
            <h4 class="mt-5 mb-3">Información Detallada de Participación</h4>
            <table class="table table-striped" style="page-break-inside: avoid;">
                <thead>
                    <tr>
                        <th colspan="2">Votación general del proyecto de ley</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        @if($project->votes()->count() > 0)
                            <td style="width: 30%; vertical-align: middle;">
                                <div id="votacion-general-pie-chart"></div>
                            </td>
                            <td>
                                De un total de {{ $project->votes()->count() }} votos totales:
                                <ul>
                                    <li>Hubo {{ $project->votos_a_favor }} a favor, correspondiente al {{ round($project->votos_a_favor/$project->votes()->count() * 100, 2) }}% de los votos.</li>
                                    <li>Hubo {{ $project->votos_en_contra }} en contra, correspondiente al {{ round($project->votos_en_contra/$project->votes()->count() * 100, 2) }}% de los votos.</li>
                                    <li>Hubo {{ $project->abstencion }} abstenciones, correspondiente al {{ round($project->abstencion/$project->votes()->count() * 100, 2) }}% de los votos.</li>
                                </ul>
                            </td>
                        @else
                            <td colspan="2">
                                <div class="alert alert-warning mb-0">
                                    <span><i class="fas fa-exclamation-circle"></i> El proyecto de ley no cuenta con votos en general.</span>
                                </div>
                            </td>
                        @endif
                    </tr>
                </tbody>
            </table>
            <table class="table table-striped" style="page-break-inside: avoid;">
                <thead>
                    <tr>
                        <th colspan="2">Participantes clasificados por género</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        @if(count($usersParticipantsOnProject) > 0)
                            <td style="width: 30%; vertical-align: middle;">
                                <div id="participantes-genero-pie-chart"></div>
                            </td>
                            <td>
                                De un total de {{ count($usersParticipantsOnProject) }} usuarios participantes:
                                <ul>
                                    @if($usersParticipantsGenderCount->male > 0) <li>Hubo {{ $usersParticipantsGenderCount->male }} de género masculino, correspondiente al {{ round($usersParticipantsGenderCount->male/count($usersParticipantsOnProject) * 100, 2) }}% de los participantes.</li> @endif
                                    @if($usersParticipantsGenderCount->female > 0) <li>Hubo {{ $usersParticipantsGenderCount->female }} de género femenino, correspondiente al {{ round($usersParticipantsGenderCount->female/count($usersParticipantsOnProject) * 100, 2) }}% de los participantes.</li> @endif
                                    @if($usersParticipantsGenderCount->other > 0) <li>Hubo {{ $usersParticipantsGenderCount->other }} de otro género, correspondiente al {{ round($usersParticipantsGenderCount->other/count($usersParticipantsOnProject) * 100, 2) }}% de los participantes.</li> @endif
                                    @if($usersParticipantsGenderCount->not_answer > 0) <li>Hubo {{ $usersParticipantsGenderCount->not_answer }} que no informa su género, correspondiente al {{ round($usersParticipantsGenderCount->not_answer/count($usersParticipantsOnProject) * 100, 2) }}% de los participantes.</li> @endif
                                </ul>
                            </td>
                        @else
                            <td colspan="2">
                                <div class="alert alert-warning mb-0">
                                    <span><i class="fas fa-exclamation-circle"></i> El proyecto de ley no cuenta con participantes.</span>
                                </div>
                            </td>
                        @endif
                    </tr>
                </tbody>
            </table>
            <table class="table table-striped" style="page-break-inside: avoid;">
                <thead>
                    <tr>
                        <th colspan="2">Participantes clasificados por rango etario</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        @if(count($usersParticipantsOnProject) > 0)
                            <td style="width: 30%; vertical-align: middle;">
                                <div id="participantes-edad-pie-chart"></div>
                            </td>
                            <td>
                                De un total de {{ count($usersParticipantsOnProject) }} usuarios participantes:
                                <ul>
                                    @if($usersParticipantsAgeRangeCount->_10_19 > 0) <li>Hubo {{ $usersParticipantsAgeRangeCount->_10_19 }} de entre 10 y 19 años de edad, correspondiente al {{ round($usersParticipantsAgeRangeCount->_10_19/$usersParticipantsAgeRangeCount->total * 100, 2) }}% de los participantes.</li> @endif
                                    @if($usersParticipantsAgeRangeCount->_20_29 > 0) <li>Hubo {{ $usersParticipantsAgeRangeCount->_20_29 }} de entre 20 y 29 años de edad, correspondiente al {{ round($usersParticipantsAgeRangeCount->_20_29/$usersParticipantsAgeRangeCount->total * 100, 2) }}% de los participantes.</li> @endif
                                    @if($usersParticipantsAgeRangeCount->_30_39 > 0) <li>Hubo {{ $usersParticipantsAgeRangeCount->_30_39 }} de entre 30 y 39 años de edad, correspondiente al {{ round($usersParticipantsAgeRangeCount->_30_39/$usersParticipantsAgeRangeCount->total * 100, 2) }}% de los participantes.</li> @endif
                                    @if($usersParticipantsAgeRangeCount->_40_49 > 0) <li>Hubo {{ $usersParticipantsAgeRangeCount->_40_49 }} de entre 40 y 49 años de edad, correspondiente al {{ round($usersParticipantsAgeRangeCount->_40_49/$usersParticipantsAgeRangeCount->total * 100, 2) }}% de los participantes.</li> @endif
                                    @if($usersParticipantsAgeRangeCount->_50_59 > 0) <li>Hubo {{ $usersParticipantsAgeRangeCount->_50_59 }} de entre 50 y 59 años de edad, correspondiente al {{ round($usersParticipantsAgeRangeCount->_50_59/$usersParticipantsAgeRangeCount->total * 100, 2) }}% de los participantes.</li> @endif
                                    @if($usersParticipantsAgeRangeCount->_60_69 > 0) <li>Hubo {{ $usersParticipantsAgeRangeCount->_60_69 }} de entre 60 y 69 años de edad, correspondiente al {{ round($usersParticipantsAgeRangeCount->_60_69/$usersParticipantsAgeRangeCount->total * 100, 2) }}% de los participantes.</li> @endif
                                    @if($usersParticipantsAgeRangeCount->_70_79 > 0) <li>Hubo {{ $usersParticipantsAgeRangeCount->_70_79 }} de entre 70 y 79 años de edad, correspondiente al {{ round($usersParticipantsAgeRangeCount->_70_79/$usersParticipantsAgeRangeCount->total * 100, 2) }}% de los participantes.</li> @endif
                                    @if($usersParticipantsAgeRangeCount->_80_89 > 0) <li>Hubo {{ $usersParticipantsAgeRangeCount->_80_89 }} de entre 80 y 89 años de edad, correspondiente al {{ round($usersParticipantsAgeRangeCount->_80_89/$usersParticipantsAgeRangeCount->total * 100, 2) }}% de los participantes.</li> @endif
                                </ul>
                                <div class="alert alert-info small">
                                    <span><i class="fas fa-info-circle"></i> Es posible que la suma de los participantes por rangos etarios no corresponda al total de participantes debido a que por fines estadísticos no se consideran edades inferiores a los 9 años y superiores a los 90 años (ambas inclusive), o bien participantes que no han informado su fecha de nacimiento.</span>
                                </div>
                            </td>
                        @else
                            <td colspan="2">
                                <div class="alert alert-warning mb-0">
                                    <span><i class="fas fa-exclamation-circle"></i> El proyecto de ley no cuenta con participantes.</span>
                                </div>
                            </td>
                        @endif
                    </tr>
                </tbody>
            </table>
            <table class="table table-striped" style="page-break-inside: avoid;">
                <thead>
                    <tr>
                        <th>Participantes clasificados por rango etario y género</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        @if(count($usersParticipantsOnProject) > 0)
                            <td class="text-center">
                                <div id="participantes-edad-genero-column-chart" style="width: 650px; height: 250px; display: inline-block;"></div>
                            </td>
                        @else
                            <td>
                                <div class="alert alert-warning mb-0">
                                    <span><i class="fas fa-exclamation-circle"></i> El proyecto de ley no cuenta con participantes.</span>
                                </div>
                            </td>
                        @endif
                    </tr>
                </tbody>
            </table>
            <table class="table table-striped" style="page-break-inside: avoid;">
                <thead>
                    <tr>
                        <th colspan="2">Participantes clasificados por región de residencia</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        @if(count($usersParticipantsOnProject) > 0)
                            <td colspan="2" class="text-center">
                                <div>
                                    <div id="participantes-region-bar-chart" style="width: 650px; display: inline-block;"></div>
                                </div>
                                <div>
                                    <div id="participantes-region-geo-chart" style="width: 550px; display: inline-block;"></div>
                                </div>
                            </td>
                        @else
                            <td>
                                <div class="alert alert-warning mb-0">
                                    <span><i class="fas fa-exclamation-circle"></i> El proyecto de ley no cuenta con participantes.</span>
                                </div>
                            </td>
                        @endif
                    </tr>
                </tbody>
            </table>
            <table class="table table-striped" style="page-break-inside: avoid;">
                <thead>
                    <tr>
                        <th colspan="2">Nube de palabras de los comentarios del proyecto de ley</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        @if($wordCloudData)
                            <td colspan="2" class="text-center">
                                <div id="word-cloud"></div>
                            </td>
                        @else
                            <td>
                                <div class="alert alert-warning mb-0">
                                    <span><i class="fas fa-exclamation-circle"></i> No se ha podido generar la nube de palabras, esto puede deberse a que no existen comentarios que procesar.</span>
                                </div>
                            </td>
                        @endif
                    </tr>
                </tbody>
            </table>
            <h5 class="mt-3 mb-2">Comentarios con Mayor Cantidad de Reacciones Totales del Proyecto de Ley en General</h5>
            <ul class="list-unstyled">
                @if(count($projectCommentsRanking->total_all_votes) > 0)
                    @foreach($projectCommentsRanking->total_all_votes as $comment)
                        @component('report.components.comment', ['comment' => $comment])
                        @endcomponent
                    @endforeach
                @else
                    <div class="alert alert-warning">
                        <span><i class="fas fa-exclamation-circle"></i> El proyecto de ley no cuenta con comentarios.</span>
                    </div>
                @endif
            </ul>
            <h5 class="mt-3 mb-2">Comentarios con Mayor Cantidad de Reacciones Positivas del Proyecto de Ley en General</h5>
            <ul class="list-unstyled">
                @if(count($projectCommentsRanking->accord_votes) > 0)
                    @foreach($projectCommentsRanking->accord_votes as $comment)
                        @component('report.components.comment', ['comment' => $comment])
                        @endcomponent
                    @endforeach
                @else
                    <div class="alert alert-warning">
                        <span><i class="fas fa-exclamation-circle"></i> El proyecto de ley no cuenta con comentarios.</span>
                    </div>
                @endif
            </ul>
            <h5 class="mt-3 mb-2">Comentarios con Mayor Cantidad de Reacciones Negativas del Proyecto de Ley en General</h5>
            <ul class="list-unstyled">
                @if(count($projectCommentsRanking->desaccord_votes) > 0)
                    @foreach($projectCommentsRanking->desaccord_votes as $comment)
                        @component('report.components.comment', ['comment' => $comment])
                        @endcomponent
                    @endforeach
                @else
                    <div class="alert alert-warning">
                        <span><i class="fas fa-exclamation-circle"></i> El proyecto de ley no cuenta con comentarios.</span>
                    </div>
                @endif
            </ul>
        </div>
        <div>
            <h3>Ideas Fundamentales</h3>
            @if(count($project->ideas) > 0)
                @foreach($project->ideas as $idea)
                    @component('report.components.idea', ['idea' => $idea, 'ideaCommentsRanking' => $ideasCommentsRanking->{$idea->id}])
                    @endcomponent
                @endforeach
            @else
                <div class="alert alert-warning">
                    <span><i class="fas fa-exclamation-circle"></i> El proyecto de ley no cuenta con ideas fundamentales.</span>
                </div>
            @endif
        </div>
        <div>
            <h3>Artículos</h3>
            @if(count($project->articles) > 0)
                @foreach($project->articles as $article)
                    @component('report.components.article', ['article' => $article, 'articleCommentsRanking' => $articlesCommentsRanking->{$article->id}])
                    @endcomponent
                @endforeach
            @else
                <div class="alert alert-warning">
                    <span><i class="fas fa-exclamation-circle"></i> El proyecto de ley no cuenta con artículos.</span>
                </div>
            @endif
        </div>
    </body>
</html>
