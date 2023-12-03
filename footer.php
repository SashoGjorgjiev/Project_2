<footer class="pt-3  text-muted text-center border-top" style="margin-top: 10rem;">
    <p id="quoteContent"></p>
    <p id="quoteAuthor"></p>
    &copy; <?php echo date('Y'); ?>
</footer>
<script>
    fetch('http://api.quotable.io/random')
        .then(response => response.json())
        .then(data => {
            document.getElementById('quoteContent').innerText = data.content;
            document.getElementById('quoteAuthor').innerText = `- ${data.author}`;
        })
        .catch(error => console.error('Error fetching quote:', error));
</script>