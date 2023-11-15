</div>

<footer class="bg-light text-center text-white">
  <!-- Footer Menu -->
  <div class="bg-secondary-subtle p-2 text-dark"> {$footer} </div>
  <!-- Footer Menu -->
  <!-- Copyright -->
  <div class="copyright bg-dark text-center p-3">
    ⓒ 2023 <a class='text-light' href='#'>NextAutoIndexPro</a>
    </div>
  <!-- Copyright -->
</footer>
<script>
    const currentDate = new Date();
    const currentYear = currentDate.getFullYear();
    const copyrightElements = document.getElementsByClassName("copyright");

    for (let i = 0; i < copyrightElements.length; i++) {
            copyrightElements[i].innerHTML = `ⓒ ${currentYear} <a class='text-light' href='#'>NextAutoIndexPro</a>`;
    }
</script>
</body>
</html>