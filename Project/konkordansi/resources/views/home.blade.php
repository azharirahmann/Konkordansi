@extends('layouts.app')

@section('title', 'Konkordansi Al-Quran')

@section('content')
    {{--cari manual--}}
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header card-header-icon" data-background-color="blue">
                    <i class="material-icons">search</i>
                </div>
                <div class="card-content">
                    <h4 class="card-title">Pencarian Manual</h4>
                    <div class="col-md-12">
                        <form id="validasi" class="form-horizontal" role="search" action="{{ route('hasilPencarian') }}" method="GET">
                            {{ csrf_field() }}
                            {{ method_field('get') }}
                            <label class="col-sm-1 label-on-left"><strong>Kata kunci</strong></label>
                            <div class="col-md-6">
                                <div class="form-group form-search is-empty">
                                    <input type="text" class="form-control" required="true" name="kata_kunci">
                                    <span class="material-input"></span>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-info btn-round btn-just-icon">
                                    <i class="material-icons">search</i>
                                    <div class="ripple-container"></div>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{--pilih surah dan ayat--}}
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header card-header-icon" data-background-color="green">
                    <i class="material-icons">assignment</i>
                </div>
                <div class="card-content">
                    <h4 class="card-title">Pilih Surah dan Ayat</h4>
                    <div class="row">
                        <form class="form-horizontal" action="{{ route('submitAya') }}" method="get">
                            {{ method_field('GET') }}
                            {{ csrf_field() }}
                            <div class="col-md-3">
                                <div class="form-group">
                                    <select name="surah" id="surah" class="selectpicker" data-style="select-with-transition" data-size="7">
{{--                                        @if(!(isset($_GET['surah']) AND isset($_GET['ayat'])))--}}
                                            <option disabled selected>Pilih Surah</option>
                                        {{--@endif--}}

                                        @foreach($suralist as $item)
                                            {{--@if(isset($_GET['surah']) AND isset($_GET['ayat']))--}}
                                                {{--@if($_GET['surah'] == $item->id)--}}
                                                    {{--<option value="{{ $item->id }}" selected>{{ $item->id }}. {{ $item->name }}</option>--}}
                                                {{--@else--}}
                                                    {{--<option value="{{ $item->id }}">{{ $item->id }}. {{ $item->name }}</option>--}}
                                                {{--@endif--}}
                                            {{--@else--}}
                                                <option value="{{ $item->id }}">{{ $item->id }}. {{ $item->name }}</option>
                                            {{--@endif--}}
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <select class="selectpicker" name="ayat" id="ayat" data-style="select-with-transition" data-size="7" required>
{{--                                        @if(!(isset($_GET['surah']) AND isset($_GET['ayat'])))--}}
                                            <option disabled selected>Pilih Ayat</option>
                                        {{--@else--}}
                                            @if(isset($_GET['surah']) AND isset($_GET['ayat']))

                                            @elseif(isset($_GET['surah']))
                                                @for($i = 1; $i <= $ayatCount; $i++)
                                                    {{--@if($_GET['ayat'] == $i)--}}
                                                    {{--<option value="{{ $i }}" selected>{{ $i }}</option>--}}
                                                    {{--@else--}}
                                                    <option value="{{ $i }}">{{ $i }}</option>
                                                    {{--@endif--}}
                                                @endfor
                                            @endif

                                        {{--@endif--}}
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-success">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    {{--Ayat Al-Quran--}}
                    <div class="row">
                        @if(isset($_GET['surah']) AND isset($_GET['ayat']))
                            <br>
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead class="text-primary">
                                            <th class="text-center">Ayat dan Terjemahan</th>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <p align="right" style="font-size: 150%;">{!! $hasilAyat->XlatAr !!}</p>
                                                    <p align="left">{!! $hasilAyat->text !!}</p>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                                    <div class="panel panel-default">
                                        <div class="panel-heading" role="tab" id="headingTwo">
                                            <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                                <h4 class="panel-title">
                                                    <div class="text-primary">
                                                        Tampilkan ayat per kata
                                                        <i class="material-icons">keyboard_arrow_down</i>
                                                    </div>
                                                </h4>
                                            </a>
                                        </div>
                                        <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
                                            <div class="panel-body">
                                                <table id="datatables" class="table table-striped table-no-bordered table-hover" cellspacing="0" width="100%" style="width:100%">
                                                    <thead>
                                                    <th>Indeks Ayat</th>
                                                    <th>Potongan Ayat</th>
                                                    <th>Terjemahan</th>
                                                    </thead>

                                                    <tbody>
                                                    @foreach($hasilAyatperKata as $item)
                                                        <tr>
                                                            <td>{{ $item->SId }}:{{ $item->VId }}:{{ $item->WordId }}</td>
                                                            <td style="font-size: 150%">{{ $item->Data }}</td>
                                                            <td><a href="#" data-toggle="modal" data-target="#pilihKonkordansi{{ $item->WordId }}">{{ $item->Translation }}</a></td>
                                                        </tr>
                                                        <div class="modal fade" id="pilihKonkordansi{{ $item->WordId }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                                            <div class="modal-dialog" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                                        <h4 class="modal-title" id="myModalLabel">Pilih Konkordansi</h4>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div class="text-center">
                                                                            <div class="col-md-4">
                                                                                <a href="{{ route('lemma', $item->Id) }}" class="btn btn-primary">Lemma</a>
                                                                            </div>
                                                                            <div class="col-md-4">
                                                                                <a href="{{ route('kataFrasa', $item->Id) }}" class="btn btn-success">Kata/Frasa</a>
                                                                            </div>
                                                                            <div class="col-md-4">
                                                                                <a href="{{ route('gen-sinonim', $item->Id) }}" class="btn btn-info">Sinonim</a>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer_script')
    <script type="text/javascript">
        function setFormValidation(id) {
            $(id).validate({
                errorPlacement: function(error, element) {
                    $(element).parent('div').addClass('has-error');
                }
            });
        }

        $(document).ready(function() {
            setFormValidation('#validasi');
        });

//        $('#surah').on('change', function (e) {
//            console.log(e);
//            var surah = e.target.value;
//
//            //ajax
//            $.get('/getAya?surah=' + surah, function (data) {
//                //success data
//                console.log(data);
//                $("#ayat").empty();
//                $.each(data, function (index, AyaObj) {
//                    $("#ayat").append('<option value="'+AyaObj.VID+'">'+AyaObj.VID+'<option>');
//                });
//                $("#ayat").selectpicker('refresh');
//            })
//        });

        $("#surah").change(function(){
            var surah = $("#surah").val();
            $.ajax({
                url: "/getAya",
                data: "surah="+surah,
                cache: false,
                success: function(msg){
                    console.log(msg);
                    $("#ayat").html(msg).selectpicker('refresh');
                }
            });
        });
    </script>

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