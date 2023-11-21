</div>

<footer class="bg-light text-center text-white">
  <!-- Footer Menu -->
  <div class="bg-secondary-subtle p-2 text-dark"> {$footer} </div>


  <!-- Copyright -->
  <div class="bg-dark text-center p-3">
    ⓒ <span id="currentYear">2023</span> <a class='text-light' href='#'>NextAutoIndexPro</a>
    </div>
</footer>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const currentDate = new Date();
        const currentYear = currentDate.getFullYear();
        const currentYearElement = document.getElementById("currentYear");

        if (currentYearElement) {
            currentYearElement.innerHTML = `${currentYear}`;
        }
    });
</script>
</body>
</html>