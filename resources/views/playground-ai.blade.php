<form action="/playground-ai" method="get">
    <textarea name="prompt" id="prompt" rows="10" cols="50"></textarea>
    <button type="submit">Kirim</button>
</form>
<div class="response">
    {{ $response ?? 'Belum ada response'}}
</div>