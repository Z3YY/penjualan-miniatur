admin : 
admin@example.com
admin123


di terminal untuk install css nya jika belum ke detect
npm install tailwindcss @tailwindcss/cli
di \assets\css\input.css
@import "tailwindcss";
npx @tailwindcss/cli -i ./assets/css/input.css -o ./assets/css/output.css --watch

alternatif
dalam
<link href="./output.css" rel="stylesheet">
ubah jadi
<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
pada header.php 
