document.addEventListener('DOMContentLoaded', function () {
	const categories = [
		{
			title: "Honor",
			desc: "უზარმაზარი შერჩევები და დაბალი ფასები დიზაინი",
			img: "./images/honor_400_pro_lunar_grey-1_1748340489-removebg-preview.png"
		},
		{
			title: "Dyson",
			desc: "თმის მოვლის ინოვაციური ტექნოლოგია",
			img: "./images/dyson.png"
		},
		{
			title: "Samsung",
			desc: "შეიძინე ყველაზე ოფიციალური პირობებით",
			img: "./images/samsung.png"
		},
		{
			title: "Honor",
			desc: "უზარმაზარი შერჩევები და დაბალი ფასები დიზაინი",
			img: "./images/honor_400_pro_lunar_grey-1_1748340489-removebg-preview.png"
		},
		{
			title: "Dyson",
			desc: "თმის მოვლის ინოვაციური ტექნოლოგია",
			img: "./images/dyson.png"
		},
		{
			title: "Samsung",
			desc: "შეიძინე ყველაზე ოფიციალური პირობებით",
			img: "./images/samsung.png"
		},
		{
			title: "Samsung",
			desc: "შეიძინე ყველაზე ოფიციალური პირობებით",
			img: "./images/samsung.png"
		},
		{
			title: "Samsung",
			desc: "შეიძინე ყველაზე ოფიციალური პირობებით",
			img: "./images/samsung.png"
		},
		{
			title: "Samsung",
			desc: "შეიძინე ყველაზე ოფიციალური პირობებით",
			img: "./images/samsung.png"
		},
		// Добавьте больше категорий по необходимости
	];

	const categorySection = document.querySelector('.category');
	if (categorySection) {
		categorySection.innerHTML = categories.map((cat, idx) => `
			<div class="category-card${idx >= 6 ? ' hidden-category' : ''}">
				<div class="category-info">
					<div class="category-title">${cat.title}</div>
					<div class="category-desc">${cat.desc}</div>
					<button class="category-btn">სრულად</button>
				</div>
				<img class="category-img" src="${cat.img}" alt="${cat.title}">
			</div>
		`).join('');
	}

	const btn = document.querySelector('.show-more-categories-btn');
	const hidden = document.querySelectorAll('.category-card.hidden-category');
	let expanded = false;
	if (btn) {
		btn.addEventListener('click', function () {
			hidden.forEach(el => el.classList.toggle('show'));
			expanded = !expanded;
			btn.textContent = expanded ? 'ნაკლები კატეგორია' : 'ყველა კატეგორია';
		});
	}
});
