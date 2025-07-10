document.addEventListener('DOMContentLoaded', function () {
	const slidesData = [
		{
			title: "iPhone 16 Pro Max",
			icons: ["M", "A", "X", "+"],
			offer: "0% განვადება - 8 თვემდე",
			price: "თვეში 462₾-დან",
			img: "/images/Apple-iPhone-16-Pro-hero-240909-lp.jpg.news_app_ed-removebg-preview.png",
			imgAlt: "iPhone 16 Pro Max"
		},
		{
			title: "Trade-In",
			icons: ["ტ", "რ", "ი", "+"],
			offer: "შეიცვალე ძველი ახალზე",
			price: "საუკეთესო პირობებით",
			img: "",
			imgAlt: "Trade-In"
		},
		{
			title: "დაზღვევა",
			icons: ["დ", "ა", "ზ", "+"],
			offer: "დაზღვევის სერვისი",
			price: "მშვიდად იყავი ტექნიკით",
			img: "",
			imgAlt: "Insurance"
		},
		{
			title: "Dyson",
			icons: ["D", "Y", "S", "+"],
			offer: "ახალი Dyson ტექნიკა",
			price: "ფასდაკლება 10%-მდე",
			img: "",
			imgAlt: "Dyson"
		},
		{
			title: "iPhone 16 Pro",
			icons: ["P", "R", "O", "+"],
			offer: "0% განვადება - 8 თვემდე",
			price: "თვეში 387₾-დან",
			img: "",
			imgAlt: "iPhone 16 Pro"
		},
		{
			title: "Insta360 X5",
			icons: ["I", "N", "S", "+"],
			offer: "360° კამერები",
			price: "ახალი მოდელები უკვე საქართველოში",
			img: "",
			imgAlt: "Insta360 X5"
		},
		{
			title: "MacBook Air M4",
			icons: ["მ", "ბ", "კ", "+"],
			offer: "0% განვადება - 12 თვემდე",
			price: "თვეში 299₾-დან",
			img: "",
			imgAlt: "MacBook Air M4"
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