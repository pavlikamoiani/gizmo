// Dynamically load header component
fetch('components/header.html')
	.then(res => res.text())
	.then(html => {
		document.getElementById('header-placeholder').innerHTML = html;
	});
// Dynamically load brand bar component
fetch('components/brand-bar.html')
	.then(res => res.text())
	.then(html => {
		document.querySelector('.brand-bar-placeholder').innerHTML = html;
	});