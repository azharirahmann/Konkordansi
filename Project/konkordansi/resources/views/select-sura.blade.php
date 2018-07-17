<option>Pilih Ayat</option>
@if(!empty($ayat))
    @foreach($ayat as $key => $value)
        <option value="{{ $key }}">{{ $value }}</option>
    @endforeach
@endif