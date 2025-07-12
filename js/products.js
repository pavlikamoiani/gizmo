document.addEventListener('DOMContentLoaded', function () {
	const products = [
		{
			discount: "-24%",
			img: "images/iphone.png",
			title: "iPhone 15",
			colors: ["#111", "#bfc2b7", "#e3e3e3", "#f7e7d7", "#e6f0f7"],
			oldPrice: "2 799₾",
			price: "2 119₾",
			monthly: "თვეში 44.15₾-დან"
		},
		{
			discount: "-33%",
			img: "images/iphone.png",
			title: "iPhone 15 Pro Max",
			colors: ["#111", "#bfc2b7", "#e3e3e3", "#f7e7d7"],
			oldPrice: "4 799₾",
			price: "4 199₾",
			monthly: "თვეში 70.81₾-დან"
		},
		{
			discount: "-21%",
			img: "images/iphone.png",
			title: "iPhone 15 Pro",
			colors: ["#111", "#bfc2b7", "#e3e3e3", "#f7e7d7"],
			oldPrice: "3 599₾",
			price: "2 999₾",
			monthly: "თვეში 62.48₾-დან"
		},
		{
			img: "images/iphone.png",
			title: "iPhone 14 Pro",
			colors: ["#6e6b6b", "#e3e3e3", "#f7e7d7", "#e6f0f7"],
			price: "4 299₾",
			monthly: "თვეში 62.48₾-დან"
		},
		{
			img: "images/iphone.png",
			title: "iPhone 14 Pro",
			colors: ["#6e6b6b", "#e3e3e3", "#f7e7d7", "#e6f0f7"],
			price: "4 299₾",
			monthly: "თვეში 62.48₾-დან"
		}
	];

	const grid = document.querySelector('.product-grid');
	if (grid) {
		grid.innerHTML = products.map(prod => `
			<div class="product-card">
				${prod.discount ? `<div class="discount-badge">${prod.discount}</div>` : ""}
				<div class="img-wrap">
					<img src="${prod.img}" alt="${prod.title}">
				</div>
				<h3>${prod.title}</h3>
				<div class="color-options">
					${prod.colors.map(c => `<span class="color-circle" style="background:${c};"></span>`).join('')}
				</div>
				<div class="price-row">
					${prod.oldPrice ? `<span class="old-price">${prod.oldPrice}</span>` : ""}
					<span class="price">${prod.price}</span>
				</div>
				<div class="monthly-payment">${prod.monthly}</div>
			</div>
		`).join('');
	}
});
