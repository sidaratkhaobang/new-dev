<form enctype="multipart/form-data" method="POST" action="{{ route('debug.upload-check') }}" >
    @csrf
    <input type="file" name="file" >
    <br>
    <input type="submit" >
</form>