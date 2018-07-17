@extends('layouts.app')

@section('title', 'Konkordansi Al-Quran')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header card-header-icon" data-background-color="blue">
                    <i class="material-icons">search</i>
                </div>
                <div class="card-content">
                    <h4 class="card-title">Pencarian Manual</h4>
                    <div class="row">
                        <div class="col-md-12">
                            <form id="validasi" class="form-horizontal" role="search" action="{{ route('hasilPencarian') }}" method="GET">
                                {{ csrf_field() }}
                                {{ method_field('get') }}
                                <label class="col-sm-2 label-on-left">Kata kunci</label>
                                <div class="col-md-6">
                                    <div class="form-group form-search is-empty">
                                        <input type="text" class="form-control" required="true" name="kata_kunci" value="">
                                        <span class="material-input"></span>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-white btn-round btn-just-icon">
                                        <i class="material-icons">search</i>
                                        <div class="ripple-container"></div>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-12">
                            @php($type = '')
                            @if(isset($hasil) === true)
                                <p>Menampilkan hasil pencarian dengan kata kunci <i><b>{!! $data->Translation !!}</b></i>, <i>{!! count($hasil) !!} hits ({!! round($time,2) !!} detik)</i></p>
                                @php($type = 'lemma')
                            @elseif(isset($hasil2) === true)
                                <p>Menampilkan hasil pencarian dengan kata kunci <i><b>{!! $data->Translation !!}</b></i>, <i>{!! count($hasil2) !!} hits ({!! round($time,2) !!} detik)</i></p>
                                @php($type = 'kataFrasa')
                            @elseif(isset($syns) === true)
                                <p>Menampilkan daftar sinonim dengan kata kunci <i><b>{!! $data->Translation !!}</b></i></p>
                                @foreach($syns as $item)
                                    <a href="{{ route('sinonim', ['id' => $data->Id, 'query' => $item, 'syns' => $syns]) }}">{{ $item }}</a>&nbsp;&nbsp; &nbsp;
                                @endforeach
                            @endif
                            @if(!empty($type))
                                <form class="form-horizontal" action="{{ route($type, $data->Id) }}">
                                    {{ csrf_field() }}
                                    {{ method_field('get') }}
                                    <div class="row">
                                        <label class="col-md-2 label-on-left">Batas Bawah</label>
                                        <div class="col-md-2">
                                            <div class="form-group label-floating is-empty">
                                                <label class="control-label"></label>
                                                <input type="number" class="form-control" name="lower" value="{{ $lower }}" required="true">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <button type="submit" class="btn btn-success btn-round btn-just-icon btn-sm">
                                                <i class="material-icons">check</i>
                                                <div class="ripple-container"></div>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            @endif
                            <br>
                            <table id="datatables" class="table table-no-bordered table-hover" cellspacing="0" width="100%" style="width:100%;margin-top:10px;">
                                <thead>
                                @if(isset($hasil) === true)
                                    <th class="text-center">Indeks Ayat</th>
                                    <th class="text-center">Ayat per Kata</th>
                                    <th class="text-center">Terjemahan Kata</th>
                                    <th class="text-center">Similarity</th>
                                    <th class="text-center">Panjang LCS</th>
                                    <th class="text-center">Ayat dan Terjemahan</th>
                                @elseif(isset($hasil2) === true)
                                    <th class="text-center">Indeks Ayat</th>
                                    <th class="text-center">Similarity</th>
                                    <th class="text-center">LCS (Panjang LCS)</th>
                                    <th class="text-center">Ayat dan Terjemahan</th>
                                @elseif(isset($syns) === true and isset($hasil3) === true)
                                    <th class="text-center">Indeks Ayat</th>
                                    <th class="text-center">Ayat per Kata</th>
                                    <th class="text-center">Terjemahan Kata</th>
                                    <th class="text-center">Ayat dan Terjemahan</th>
                                @endif
                                </thead>

                                <tbody>
                                @if(isset($hasil) === true)
                                    @foreach($hasil as $item)
                                        <tr>
                                            <td>{{ $item[1] }}:{{ $item[2] }}:{{ $item[3] }}</td>
                                            <td>
                                                <p align="center" style="font-size: 120%">
                                                    {!! $item[10] !!}
                                                </p>
                                            </td>
                                            <td>{!! $item[8] !!}</td>
                                            <td>{{ $item[9] }}%</td>
                                            <td>{{ strlen($item[7]) }}</td>
                                            <td>
                                                <p align="right" style="font-size: 120%">
                                                    {!! $item[5] !!}
                                                </p>
                                                <br>
                                                <p align="left">
                                                    {!! $item[6] !!}
                                                </p>
                                            </td>
                                        </tr>
                                    @endforeach
                                @elseif(isset($hasil2) === true)
                                    @foreach($hasil2 as $item)
                                        <tr>
                                            <td>{{ $item[1] }}:{{ $item[2] }} ({{ $item[9] }}:{{ $item[10] }})</td>
                                            <td>{{ $item[8] }}%</td>
                                            <td>{{ $item[6] }} ({{ strlen($item[6]) }})</td>
                                            <td>
                                                <p align="right" style="font-size: 120%">
                                                    {!! $item[3] !!}
                                                </p>
                                                <br>
                                                <p align="left">
                                                    {!! $item[7] !!}
                                                </p>
                                            </td>
                                        </tr>
                                    @endforeach
                                @elseif(isset($syns) === true and isset($hasil3) === true)
                                    @foreach($hasil3 as $item)
                                        <tr>
                                            <td>{{ $item->SId }}:{{ $item->WordId }}:{{ $item->WordId }}</td>
                                            <td>{{ $item->Data }}</td>
                                            <td>{{ $item->Translation }}</td>
                                            <td>
                                                <p align="right" style="font-size: 120%">
                                                    {!! $item->XlatAr !!}
                                                </p>
                                                <br>
                                                <p align="left">
                                                    {!! $item->text!!}
                                                </p>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer_script')
    <!--  DataTables.net Plugin    -->
    <script src="{{ asset('backend/js/jquery.datatables.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            $('#datatables').DataTable({
                "aaSorting": [],
                responsive: true,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search records"
                }

            });

            $('.card .material-datatables label').addClass('form-group');
        });

//        function loadTable(){
//            var target = event.target.value;
//            var example_table = $('#datatables').DataTable({
//                'ajax': {
//                    "type"   : "GET",
//                    "url"    : '/get-result-sinonim',
//                    "data"   : function( d ) {
//                        d.query = target;
//                    },
//                    "dataSrc": ""
//                },
//                'columns': [
//                    {"data" : "SId"},
//                    {"data" : "Translation"},
//                    {"data" : "Data"}
//                ]
//            });
//
//            example_table.ajax.reload()
//        }
    </script>
@endsection