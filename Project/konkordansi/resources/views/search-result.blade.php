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
                                        <input type="text" class="form-control" required="true" name="kata_kunci" value="{{ $kata_kunci }}">
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
                            <p>Menampilkan hasil pencarian dengan kata kunci <i><b>{!! $kata_kunci !!}</b></i>, <i>{!! count($hasil) !!} hits ({!! round($time,2) !!} detik)</i></p>
                            <br>
                            <form class="form-horizontal" action="{{ route('hasilPencarianApply', $kata_kunci) }}">
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
                            <table id="datatables" class="table table-no-bordered table-hover" cellspacing="0" style="margin-top:10px;">
                                <thead>
                                <th>Surah</th>
                                <th>Similarity</th>
                                <th></th>
                                <th></th>
                                </thead>

                                <tbody>
                                {{--solusi nya pindah fungsi yg ada di bawah ini ke fungsi di homecontroller nya--}}
                                @php($i = 0)
                                @foreach($hasil as $item)
                                    <tr>
                                        <td>{{ $item[1] }}:{{ $item[2] }}</td>
                                        <td>{{ $item[8] }}%</td>
                                        <td class="text-center">{!! substr($item[7], $item[9], $item[10]) !!}</td>
                                        <!-- Button trigger modal -->
                                        <td class="text-center">
                                            <a href="#" data-toggle="modal" data-target="#myModal{{ $i }}" class="btn-just-icon">
                                                <i class="material-icons">info</i>
                                            </a>
                                        </td>
                                        <!-- End trigger modal -->
                                        <!-- Modal -->
                                        <div class="modal fade" id="myModal{{ $i }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                            <div class="modal-dialog modal-lg" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title" id="myModalLabel"><strong>Detil Hasil Pencarian</strong></h4>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="col-md-12">
                                                            <h4>Indeks Ayat : ({!! $item[1] !!}:{!! $item[2] !!})</h4>
                                                            <h4>Similarity : {!! $item[8] !!}%</h4>
                                                            <h4>LCS (Panjang LCS) : {!! $item[6] !!}({!! strlen($item[6]) !!})</h4>
                                                            <h4 style="margin-bottom: 10px;">Ayat dan Terjemahan :</h4>
                                                            <div class="text-right" style="font-size: 180%;">
                                                                <p>{!! $item[3] !!}</p>
                                                            </div>
                                                            <br>
                                                            <div class="text-left">
                                                                <p>{!! $item[7] !!}</p>
                                                            </div>
                                                            <br>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </tr>
                                    @php($i++)
                                @endforeach
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
    </script>
@endsection