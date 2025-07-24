document.addEventListener('DOMContentLoaded', function () {
	const slidesData = [
		{
			title: "Sony PS4 dualshock",
			icons: ["P", "S", "4", "+"],
			offer: "0% განვადება - 8 თვემდე",
			price: "თვეში 120₾-დან",
			img: "images/playstation.png",
			imgAlt: "Sony PS4 dualshock"
		},
		{
			title: "Gaming console",
			icons: ["G", "M", "C", "+"],
			offer: "შეიცვალე ძველი ახალზე",
			price: "საუკეთესო პირობებით",
			img: "images/console.png",
			imgAlt: "Gaming console"
		},
		{
			title: "i9 Pro Max",
			icons: ["I", "9", "P", "+"],
			offer: "დაზღვევის სერვისი",
			price: "მშვიდად იყავი ტექნიკით",
			img: "images/i9ProMax.png",
			imgAlt: "i9 Pro Max"
		},
		{
			title: "Baby smart watch 2G",
			icons: ["B", "S", "W", "+"],
			offer: "მშვენიერი საჩუქარი",
			price: "ფასდაკლება 10%-მდე",
			img: "images/babysmart.png",
			imgAlt: "Baby smart watch 2G"
		},
		{
			title: "Led Photographic Light RL26",
			icons: ["L", "P", "L", "+"],
			offer: "0% განვადება - 8 თვემდე",
			price: "თვეში 120₾-დან",
			img: "images/led.png",
			imgAlt: "Led Photographic Light RL26"
		},
		{
			title: "iSteady სტაბილიზატორი",
			icons: ["I", "S", "E", "+"],
			offer: "0% განვადება - 8 თვემდე",
			price: "თვეში 120₾-დან",
			img: "images/isteady.png",
			imgAlt: "iSteady სტაბილიზატორი"
		},
		{
			title: "Earldom Projector",
			icons: ["E", "P", "R", "+"],
			offer: "0% განვადება - 12 თვემდე",
			price: "საუკეთესო პირობებით",
			img: "images/Projector.png",
			imgAlt: "Earldom Projector"
		}
	];

	const slidesContainer = document.querySelector('.custom-slider-content');
	// Render slides before selecting menuBtns!
	slidesContainer.innerHTML = slidesData.map((slide, idx) => `
		<div class="slider-slide${idx === 0 ? ' active' : ''}">
			<div class="slider-left">
				<h1>${slide.title}</h1>
				<div class="slider-icons">
					${slide.icons.map((icon, i) =>
		`<span class="slider-icon${i === 3 ? ' plus' : ''}">${icon}</span>`
	).join('')}
				</div>
				<div class="slider-offer">${slide.offer}</div>
				<div class="slider-price">${slide.price}</div>
			</div>
			<div class="slider-right">
				<img src="${slide.img}" alt="${slide.imgAlt}">
			</div>
		</div>
	`).join('');

	const slides = document.querySelectorAll('.slider-slide');
	const menuBtns = document.querySelectorAll('.custom-slider-menu .slider-menu-btn');
	let current = 0;
	let timer;

	function showSlide(idx) {
		slides.forEach((slide, i) => {
			if (i === idx) {
				slide.classList.add('active');
			} else {
				slide.classList.remove('active');
			}
			if (menuBtns[i]) {
				menuBtns[i].classList.toggle('active', i === idx);
			}
		});
		current = idx;
	}

	function nextSlide() {
		showSlide((current + 1) % slides.length);
	}

	menuBtns.forEach((btn, i) => {
		btn.onclick = () => {
			showSlide(i);
			restartAuto();
		};
	});

	function startAuto() {
		stopAuto();
		timer = setInterval(nextSlide, 5000);
	}
	function stopAuto() {
		if (timer) clearInterval(timer);
	}
	function restartAuto() {
		stopAuto();
		startAuto();
	}

	const slider = document.querySelector('.custom-slider');
	if (slider) {
		slider.addEventListener('mouseenter', stopAuto);
		slider.addEventListener('mouseleave', startAuto);
	}

	showSlide(0);
	startAuto();
});	