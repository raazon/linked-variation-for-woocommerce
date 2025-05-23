document.addEventListener("DOMContentLoaded", function () {
	const attributes = document.querySelectorAll(".lvfw-attribute");

	attributes.forEach(attribute => {
		const currentName = attribute.querySelector(".lvfw-current-name");
		const items = attribute.querySelectorAll(".lvfw-attribute-options .lvfw-product");

		let activeItem = attribute.querySelector(".lvfw-product.active");
		let activeTitle = activeItem ? activeItem.getAttribute("data-title") : currentName.textContent.trim();

		items.forEach(item => {
			item.addEventListener("mouseenter", () => {
				const title = item.getAttribute("data-title");
				if (title) currentName.textContent = title;
			});

			item.addEventListener("mouseleave", () => {
				currentName.textContent = activeTitle;
			});
		});
	});
});