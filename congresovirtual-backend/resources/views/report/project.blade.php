<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap.min.css') }}" >

        <!-- Load d3.js -->
        <script src="{{ asset('js/d3.v4.js') }}"></script>

        <!-- Load d3-cloud -->
        <script src="{{ asset('js/d3.layout.cloud.js') }}"></script>

        {{-- make sure you are using http, and not https --}}
        <script type="text/javascript" src="http://www.google.com/jsapi"></script>

        <script type="text/javascript">
            function init() {
                google.load('visualization', '1.1', {
                    packages: [
                        'corechart',
                        'geochart'
                    ],
                    callback: 'drawCharts'
                });
            }

            function drawCharts() {
                var votacionGeneralPieChart = new google.visualization.PieChart(document.getElementById('votacion-general-pie-chart'));
                votacionGeneralPieChart.draw(
                    google.visualization.arrayToDataTable([
                        ['Voto', 'Cantidad'],
                        ['A favor', {{ $project->votos_a_favor }} ],
                        ['En contra', {{ $project->votos_en_contra }} ],
                        ['Abstención', {{ $project->abstencion }} ]
                    ]),
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

                var participantesGeneroPieChart = new google.visualization.PieChart(document.getElementById('participantes-genero-pie-chart'));
                participantesGeneroPieChart.draw(
                    google.visualization.arrayToDataTable([
                        ['Género', 'Cantidad'],
                        ['Masculino', {{ $usersParticipantsGenderCount->male }} ],
                        ['Femenino', {{ $usersParticipantsGenderCount->female }} ],
                        ['Otro', {{ $usersParticipantsGenderCount->other }} ],
                        ['No responde', {{ $usersParticipantsGenderCount->not_answer }} ]
                    ]),
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

                var participantesEdadPieChart = new google.visualization.PieChart(document.getElementById('participantes-edad-pie-chart'));
                participantesEdadPieChart.draw(
                    google.visualization.arrayToDataTable([
                        ['Rango de edad', 'Cantidad'],
                        ['10-19', {{ $usersParticipantsAgeRangeCount->_10_19 }} ],
                        ['20-29', {{ $usersParticipantsAgeRangeCount->_20_29 }} ],
                        ['30-39', {{ $usersParticipantsAgeRangeCount->_30_39 }} ],
                        ['40-49', {{ $usersParticipantsAgeRangeCount->_40_49 }} ],
                        ['50-59', {{ $usersParticipantsAgeRangeCount->_50_59 }} ],
                        ['60-69', {{ $usersParticipantsAgeRangeCount->_60_69 }} ],
                        ['70-79', {{ $usersParticipantsAgeRangeCount->_70_79 }} ],
                        ['80-89', {{ $usersParticipantsAgeRangeCount->_80_89 }} ]
                    ]),
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

                var test = @json($project_detail);
                // List of words
                var myWords = test.split(" ");

                // set the dimensions and margins of the graph
                var margin = {top: 10, right: 10, bottom: 10, left: 10},
                    width = 450 - margin.left - margin.right,
                    height = 450 - margin.top - margin.bottom;

                // append the svg object to the body of the page
                var svg = d3.select("#my_dataviz").append("svg")
                    .attr("width", width + margin.left + margin.right)
                    .attr("height", height + margin.top + margin.bottom)
                    .append("g")
                    .attr("transform",
                          "translate(" + margin.left + "," + margin.top + ")");

                // Constructs a new cloud layout instance. It run an algorithm to find the position of words that suits your requirements
                var layout = d3.layout.cloud()
                    .size([width, height])
                    .words(myWords.map(function(d) { return {text: d}; }))
                    .padding(10)
                    .fontSize(60)
                    .on("end", draw);
                layout.start();

                // This function takes the output of 'layout' above and draw the words
                // Better not to touch it. To change parameters, play with the 'layout' variable above
                function draw(words) {
                    svg
                        .append("g")
                        .attr("transform", "translate(" + layout.size()[0] / 2 + "," + layout.size()[1] / 2 + ")")
                        .selectAll("text")
                        .data(words)
                        .enter().append("text")
                        .style("font-size", function(d) { return d.size + "px"; })
                        .attr("text-anchor", "middle")
                        .attr("transform", function(d) {
                            return "translate(" + [d.x, d.y] + ")rotate(" + d.rotate + ")";
                        })
                        .text(function(d) { return d.text; });
                }
            }
        </script>
    </head>
    <body onload="init()">
        <div class="page-header text-center">
            <h1>
                Reporte Generado por {{ config('app.name') }}<br>
                para el Proyecto de Ley:
            </h1>
            <h2>
                {{ $project->titulo }}<br>
                <small>N° de Boletín: {{ $project->boletin }}</small>
            </h2>
        </div>
        <div>
            <h3>Información General del Proyecto de Ley</h3>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th style="width: 30%;">Atributo</th>
                        <th>Valor</th>
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
                        <td class="ellipsis" style="max-height: 60px;">{{ $project->resumen }}</td>
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
            <h3>Información General de Participación</h3>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th style="width: 30%;">Atributo</th>
                        <th>Valor</th>
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
                        <td>{{ $fechaInicio->format('d/m/Y H:m:s') }} Hrs. (UTC)</td>
                    </tr>
                    <tr>
                        <th>Fecha de término de las votaciones</th>
                        <td>{{ $fechaTermino->format('d/m/Y H:m:s') }} Hrs. (UTC)</td>
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
                <h4 class="alert-heading">Nota</h4>
                <p>Un usuario se considera participante si éste realizó un voto o al menos un comentario en el proyecto de ley.</p>
            </div>
        </div>
        <div style="display: block; page-break-before: always;"></div>
        <div>
            <h3>Información Detallada de Participación</h3>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th style="width: 20%;">Indicador</th>
                        <th style="width: 30%">Gráfico</th>
                        <th>Detalles</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th>Votación general del proyecto de ley</th>
                        <td>
                            <div id="votacion-general-pie-chart"></div>
                        </td>
                        <td>
                            @if($project->votes()->count() > 1)
                                De un total de {{ $project->votes()->count() }} votos totales:
                                <ul>
                                    <li>Hubo {{ $project->votos_a_favor }} a favor, correspondiente al {{ round($project->votos_a_favor/$project->votes()->count() * 100, 2) }}% de los votos.</li>
                                    <li>Hubo {{ $project->votos_en_contra }} en contra, correspondiente al {{ round($project->votos_en_contra/$project->votes()->count() * 100, 2) }}% de los votos.</li>
                                    <li>Hubo {{ $project->abstencion }} abstenciones, correspondiente al {{ round($project->abstencion/$project->votes()->count() * 100, 2) }}% de los votos.</li>
                                </ul>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Participantes clasificados por género</th>
                        <td>
                            <div id="participantes-genero-pie-chart"></div>
                        </td>
                        <td>
                            @if(count($usersParticipantsOnProject) > 1)
                                De un total de {{ count($usersParticipantsOnProject) }} usuarios participantes:
                                <ul>
                                    @if($usersParticipantsGenderCount->male > 0) <li>Hubo {{ $usersParticipantsGenderCount->male }} de género masculino, correspondiente al {{ round($usersParticipantsGenderCount->male/count($usersParticipantsOnProject) * 100, 2) }}% de los participantes.</li> @endif
                                    @if($usersParticipantsGenderCount->female > 0) <li>Hubo {{ $usersParticipantsGenderCount->female }} de género femenino, correspondiente al {{ round($usersParticipantsGenderCount->female/count($usersParticipantsOnProject) * 100, 2) }}% de los participantes.</li> @endif
                                    @if($usersParticipantsGenderCount->other > 0) <li>Hubo {{ $usersParticipantsGenderCount->other }} de otro género, correspondiente al {{ round($usersParticipantsGenderCount->other/count($usersParticipantsOnProject) * 100, 2) }}% de los participantes.</li> @endif
                                    @if($usersParticipantsGenderCount->not_answer > 0) <li>Hubo {{ $usersParticipantsGenderCount->not_answer }} que no informa su género, correspondiente al {{ round($usersParticipantsGenderCount->not_answer/count($usersParticipantsOnProject) * 100, 2) }}% de los participantes.</li> @endif
                                </ul>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Participantes clasificados por rango etario</th>
                        <td>
                            <div id="participantes-edad-pie-chart"></div>
                        </td>
                        <td>
                            @if(count($usersParticipantsOnProject) > 1)
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
                            @endif
                            <div class="alert alert-info small">
                                <h4 class="alert-heading">Nota</h4>
                                <p>Es posible que la suma de los participantes por rangos etarios no corresponda al total de participantes debido a que por fines estadísticos no se consideran edades inferiores a los 9 años y superiores a los 90 años (ambas inclusive), o bien participantes que no han informado su fecha de nacimiento.</p>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>Participantes clasificados por rango etario y género</th>
                        <td colspan="2">
                            <div id="participantes-edad-genero-column-chart" style="width: 500px; height: 250px;"></div>
                        </td>s
                    </tr>
                    <tr>
                        <th>Participantes clasificados por región de residencia</th>
                        <td colspan="2">
                            <div id="participantes-region-geo-chart" style="width: 500px;"></div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>





        <h2>Participantes</h2>
        <div class="row">
            <div class="col-xs-6">
                <h4>Votación general</h4>
                <div id="piechart" class="pie-chart" style="width: 450px; height: 250px;"></div>
            </div>
            <div class="col-xs-6">
                <h4>Votación por artículos</h4>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Artículo</th>
                            <th>Resultados</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($articles as $key => $article)
                            <tr>
                                <th scope="row">{{ $article->titulo }}</th>
                                <td>
                                    <div class="progress">
                                        @if($article->votos_a_favor + $article->votos_en_contra + $article->abstencion > 0)
                                            <div class="progress-bar progress-bar-success" style="width: {{ 100 }}%">
                                                <span class="sr-only">A favor</span>
                                            </div>
                                            <div class="progress-bar progress-bar-danger" style="width: {{ 100 }}%">
                                                <span class="sr-only">En contra</span>
                                            </div>
                                            <div class="progress-bar" style="width: {{ 100 }}% ; background-color: #aca9a9;">
                                                <span class="sr-only">Abstención</span>
                                            </div>
                                        @else
                                            <div class="progress-bar progress-bar-success" style="width: 0%">
                                                <span class="sr-only">A favor</span>
                                            </div>
                                            <div class="progress-bar progress-bar-danger" style="width: 0%">
                                                <span class="sr-only">En contra</span>
                                            </div>
                                            <div class="progress-bar" style="width: 0% ; background-color: #aca9a9;">
                                                <span class="sr-only">Abstención</span>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="col-xs-6">
                <h4>Votación por género</h4>
                <div id="piechart2" class="pie-chart" style="width: 450px; height: 250px;"></div>
            </div>
            <div class="col-xs-6">
                <h4>Edades</h4>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Rango</th>
                            <th>Frecuencia</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th scope="row">0-18</th>
                            <td>0</td>
                        </tr>
                        <tr>
                            <th scope="row">19-26</th>
                            <td>2</td>
                        </tr>
                        <tr>
                            <th scope="row">27-40</th>
                            <td>1</td>
                        </tr>
                        <tr>
                            <th scope="row">41-50</th>
                            <td>1</td>
                        </tr>
                        <tr>
                            <th scope="row">51-60</th>
                            <td>1</td>
                        </tr>
                        <tr>
                            <th scope="row">+60</th>
                            <td>1</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-xs-6">
                <h4>Artículos más comentados</h4>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Artículo</th>
                            <th>Cantidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($articles as $key => $article)
                            @if($key < 5)
                                <tr>
                                    <th scope="row">{{ $article->titulo }}</th>
                                    <td>{{ $article->comments_count }}</td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="col-xs-6">
                <h4>Artículos más comentados por edades</h4>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Artículo</th>
                            <th>0-18</th>
                            <th>19-26</th>
                            <th>27-40</th>
                            <th>41-50</th>
                            <th>51-60</th>
                            <th>+60</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($articles as $key => $article)
                            @if($key < 5)
                                <tr>
                                    <th scope="row">{{ $article->titulo }}</th>
                                    <td>{{ $article->comments_count }}</td>
                                    <td>0</td>
                                    <td>0</td>
                                    <td>0</td>
                                    <td>0</td>
                                    <td>0</td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="col-xs-6">
                <div id="my_dataviz"></div>
            </div>
            <!-- Add the extra clearfix for only the required viewport -->
            <div class="clearfix visible-xs-block"></div>
        </div>
        <h2>Artículos</h2>
        <div class="row">
            <div class="col-xs-12">
                @foreach($articles as $article)
                    <h3>{{ $article->titulo }}</h3>
                    <ul class="media-list">
                        @foreach($article->comments as $comment)
                            <li class="media">
                                <div class="media-body">
                                    <h4 class="media-heading">{{ $comment->user->name }} {{ $comment->user->surname }}
                                        <span class="label label-success">{{ $comment->votos_a_favor }}</span>
                                        <span class="label label-danger">{{ $comment->votos_en_contra }}</span>
                                    </h4>
                                    {{ $comment->body }}
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endforeach
            </div>
        </div>
    </body>
</html>
