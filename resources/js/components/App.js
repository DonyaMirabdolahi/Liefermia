import React, { useState, useEffect } from "react";

function App() {
    const [items, setItems] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const [selectedItem, setSelectedItem] = useState(null);
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [itemDetails, setItemDetails] = useState(null);
    const [selectedSize, setSelectedSize] = useState(null);
    const [selectedExtras, setSelectedExtras] = useState({});
    const [quantity, setQuantity] = useState(1);
    const [totalPrice, setTotalPrice] = useState(0);
    const [loadingDetails, setLoadingDetails] = useState(false);
    const [showSuccessModal, setShowSuccessModal] = useState(false);

    useEffect(() => {
        fetch("/api/items")
            .then((response) => {
                if (!response.ok) {
                    throw new Error("Network response was not ok");
                }
                return response.json();
            })
            .then((data) => {
                setItems(data);
                setLoading(false);
            })
            .catch((error) => {
                console.error("Error fetching items:", error);
                setError(error.message);
                setLoading(false);
            });
    }, []);

    useEffect(() => {
        if (selectedSize && itemDetails) {
            calculateTotalPrice(selectedSize, selectedExtras, quantity);
        }
    }, [selectedSize, selectedExtras, quantity, itemDetails]);

    const openModal = async (item) => {
        setSelectedItem(item);
        setIsModalOpen(true);
        setLoadingDetails(true);
        setSelectedSize(null);
        setSelectedExtras({});
        setQuantity(1);
        setTotalPrice(0);

        try {
            const response = await fetch(`/api/items/${item.id}/details`);
            if (!response.ok) {
                throw new Error("Failed to fetch item details");
            }
            const details = await response.json();
            setItemDetails(details);
            setLoadingDetails(false);
        } catch (error) {
            console.error("Error fetching item details:", error);
            setLoadingDetails(false);
        }
    };

    const closeModal = () => {
        setIsModalOpen(false);
        setSelectedItem(null);
        setItemDetails(null);
        setSelectedSize(null);
        setSelectedExtras({});
        setQuantity(1);
        setTotalPrice(0);
    };

    const handleSizeChange = (size) => {
        setSelectedSize(size);
        setSelectedExtras({});
    };

    const handleExtraChange = (ruleId, extraId, isChecked) => {
        const newSelectedExtras = { ...selectedExtras };

        if (!newSelectedExtras[ruleId]) {
            newSelectedExtras[ruleId] = [];
        }

        if (isChecked) {
            newSelectedExtras[ruleId].push(extraId);
        } else {
            newSelectedExtras[ruleId] = newSelectedExtras[ruleId].filter(
                (id) => id !== extraId
            );
        }

        setSelectedExtras(newSelectedExtras);
    };

    const handleQuantityChange = (newQuantity) => {
        if (newQuantity >= 1) {
            setQuantity(newQuantity);
        }
    };

    const calculateTotalPrice = (size, extras, qty) => {
        if (!size) return;

        let basePrice = size.price;
        let extrasPrice = 0;

        Object.keys(extras).forEach((ruleId) => {
            const rule = itemDetails.rules.find((r) => r.id == ruleId);
            if (rule) {
                (extras[ruleId] || []).forEach((extraId) => {
                    const extra = rule.extras.find((e) => e.id == extraId);
                    if (extra && extra.prices[size.id]) {
                        extrasPrice += extra.prices[size.id];
                    }
                });
            }
        });

        const total = (basePrice + extrasPrice) * qty;
        setTotalPrice(total);
    };

    const handleSubmit = async () => {
        if (!selectedSize) {
            alert("Please select a size");
            return;
        }

        const sizeInfo = selectedSize;

        let extrasInfo = [];
        if (itemDetails && itemDetails.rules) {
            Object.keys(selectedExtras).forEach((ruleId) => {
                const rule = itemDetails.rules.find((r) => r.id == ruleId);
                if (rule) {
                    (selectedExtras[ruleId] || []).forEach((extraId) => {
                        const extra = rule.extras.find((e) => e.id == extraId);
                        if (extra) {
                            extrasInfo.push({
                                rule_id: rule.id,
                                rule_name: rule.name,
                                extra_id: extra.id,
                                extra_name: extra.name,
                                extra_price: extra.prices[selectedSize.id] || 0,
                            });
                        }
                    });
                }
            });
        }

        const orderData = {
            item_id: selectedItem.id,
            item_name: selectedItem.name,
            size: sizeInfo,
            quantity: quantity,
            extras: extrasInfo,
            total_price: totalPrice,
        };

        console.log("Order data being sent to server:", orderData);
        try {
            const response = await fetch("/items", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN":
                        document
                            .querySelector('meta[name="csrf-token"]')
                            ?.getAttribute("content") || "",
                },
                body: JSON.stringify(orderData),
            });

            if (response.ok) {
                setShowSuccessModal(true);
                closeModal();
            } else {
                alert("Failed to submit order");
            }
        } catch (error) {
            console.error("Error submitting order:", error);
            alert("Error submitting order");
        }
    };

    const canSelectExtra = (ruleId, extraId) => {
        const rule = itemDetails?.rules.find((r) => r.id == ruleId);
        if (!rule) return false;

        const currentSelected = selectedExtras[ruleId] || [];

        if (rule.max_option && currentSelected.length >= rule.max_option) {
            return currentSelected.includes(extraId);
        }

        const totalSelected = Object.values(selectedExtras).flat().length;
        if (
            itemDetails?.max_option &&
            totalSelected >= itemDetails.max_option
        ) {
            return currentSelected.includes(extraId);
        }

        return true;
    };

    if (loading) {
        return React.createElement(
            "div",
            { className: "container mx-auto px-4 py-8" },
            React.createElement(
                "h1",
                { className: "text-3xl font-bold mb-6" },
                "Loading pizzas..."
            )
        );
    }

    if (error) {
        return React.createElement(
            "div",
            { className: "container mx-auto px-4 py-8" },
            React.createElement(
                "h1",
                { className: "text-3xl font-bold mb-6" },
                "Error loading data"
            ),
            React.createElement("p", { className: "text-red-600" }, error)
        );
    }

    return React.createElement(
        React.Fragment,
        null,
        React.createElement(
            "div",
            { className: "container mx-auto px-4 py-8" },
            React.createElement(
                "h1",
                { className: "text-3xl font-bold mb-6" },
                "Available Pizzas"
            ),
            React.createElement(
                "div",
                {
                    className:
                        "grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6",
                },
                items.map((item) =>
                    React.createElement(
                        "div",
                        {
                            key: item.id,
                            className:
                                "bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow",
                        },
                        React.createElement(
                            "div",
                            { className: "p-4" },
                            React.createElement(
                                "h2",
                                { className: "text-xl font-semibold mb-2" },
                                item.name
                            ),
                            React.createElement(
                                "div",
                                { className: "flex items-center space-x-4" },
                                React.createElement(
                                    "div",
                                    {
                                        className:
                                            "w-24 h-24 relative flex-shrink-0",
                                    },
                                    React.createElement("img", {
                                        src: item.image_url,
                                        alt: item.name,
                                        className:
                                            "w-full h-full object-cover rounded-lg",
                                    })
                                ),
                                React.createElement(
                                    "div",
                                    { className: "flex-grow" },
                                    React.createElement(
                                        "p",
                                        {
                                            className:
                                                "text-gray-600 text-sm mb-1",
                                        },
                                        `ID: ${item.id}`
                                    ),
                                    React.createElement(
                                        "p",
                                        {
                                            className:
                                                "text-green-600 font-semibold text-lg",
                                        },
                                        `From $${item.min_price}`
                                    )
                                )
                            ),
                            React.createElement(
                                "button",
                                {
                                    onClick: () => openModal(item),
                                    className:
                                        "mt-4 w-full bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition-colors",
                                },
                                "View Details"
                            )
                        )
                    )
                )
            ),
            isModalOpen &&
                React.createElement(
                    "div",
                    {
                        className:
                            "fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4",
                        onClick: closeModal,
                    },
                    React.createElement(
                        "div",
                        {
                            className:
                                "bg-white rounded-lg p-6 max-w-2xl w-full max-h-[90vh] overflow-y-auto",
                            onClick: (e) => e.stopPropagation(),
                        },
                        React.createElement(
                            "div",
                            {
                                className:
                                    "flex justify-between items-center mb-4",
                            },
                            React.createElement(
                                "h2",
                                { className: "text-2xl font-bold" },
                                selectedItem?.name
                            ),
                            React.createElement(
                                "button",
                                {
                                    onClick: closeModal,
                                    className:
                                        "text-gray-500 hover:text-gray-700 text-2xl font-bold",
                                },
                                "×"
                            )
                        ),
                        loadingDetails &&
                            React.createElement(
                                "div",
                                { className: "text-center py-8" },
                                React.createElement(
                                    "p",
                                    null,
                                    "Loading details..."
                                )
                            ),
                        !loadingDetails &&
                            itemDetails &&
                            React.createElement(
                                "div",
                                { className: "space-y-6" },
                                React.createElement(
                                    "div",
                                    { className: "mb-4" },
                                    React.createElement("img", {
                                        src: itemDetails.image_url,
                                        alt: itemDetails.name,
                                        className:
                                            "w-full h-48 object-cover rounded-lg",
                                    })
                                ),
                                React.createElement(
                                    "div",
                                    { className: "space-y-2" },
                                    React.createElement(
                                        "h3",
                                        { className: "text-lg font-semibold" },
                                        "Select Size"
                                    ),
                                    React.createElement(
                                        "div",
                                        { className: "grid grid-cols-2 gap-2" },
                                        itemDetails.sizes.map((size) =>
                                            React.createElement(
                                                "button",
                                                {
                                                    key: size.id,
                                                    onClick: () =>
                                                        handleSizeChange(size),
                                                    className: `p-3 border rounded-lg text-left transition-colors ${
                                                        selectedSize?.id ===
                                                        size.id
                                                            ? "border-blue-500 bg-blue-50"
                                                            : "border-gray-300 hover:border-gray-400"
                                                    }`,
                                                },
                                                React.createElement(
                                                    "div",
                                                    {
                                                        className:
                                                            "font-medium",
                                                    },
                                                    size.name
                                                ),
                                                React.createElement(
                                                    "div",
                                                    {
                                                        className:
                                                            "text-green-600",
                                                    },
                                                    `$${size.price}`
                                                )
                                            )
                                        )
                                    )
                                ),
                                selectedSize &&
                                    React.createElement(
                                        "div",
                                        { className: "space-y-4" },
                                        itemDetails.rules.map((rule) =>
                                            React.createElement(
                                                "div",
                                                {
                                                    key: rule.id,
                                                    className: "space-y-2",
                                                },
                                                React.createElement(
                                                    "h4",
                                                    {
                                                        className:
                                                            "font-semibold",
                                                    },
                                                    `${rule.name} ${
                                                        rule.max_option
                                                            ? `(Max: ${rule.max_option})`
                                                            : ""
                                                    }`
                                                ),
                                                rule.field_type === "dropdown"
                                                    ? React.createElement(
                                                          "select",
                                                          {
                                                              onChange: (e) => {
                                                                  const extraId =
                                                                      parseInt(
                                                                          e
                                                                              .target
                                                                              .value
                                                                      );
                                                                  if (extraId) {
                                                                      handleExtraChange(
                                                                          rule.id,
                                                                          extraId,
                                                                          true
                                                                      );
                                                                  }
                                                              },
                                                              className:
                                                                  "w-full p-2 border border-gray-300 rounded-lg",
                                                          },
                                                          React.createElement(
                                                              "option",
                                                              { value: "" },
                                                              "Select an option"
                                                          ),
                                                          rule.extras.map(
                                                              (extra) =>
                                                                  React.createElement(
                                                                      "option",
                                                                      {
                                                                          key: extra.id,
                                                                          value: extra.id,
                                                                      },
                                                                      `${
                                                                          extra.name
                                                                      } ${
                                                                          extra
                                                                              .prices[
                                                                              selectedSize
                                                                                  .id
                                                                          ]
                                                                              ? `(+$${
                                                                                    extra
                                                                                        .prices[
                                                                                        selectedSize
                                                                                            .id
                                                                                    ]
                                                                                })`
                                                                              : ""
                                                                      }`
                                                                  )
                                                          )
                                                      )
                                                    : React.createElement(
                                                          "div",
                                                          {
                                                              className:
                                                                  "space-y-2",
                                                          },
                                                          rule.extras.map(
                                                              (extra) =>
                                                                  React.createElement(
                                                                      "label",
                                                                      {
                                                                          key: extra.id,
                                                                          className: `flex items-center space-x-2 p-2 border rounded-lg cursor-pointer ${
                                                                              (
                                                                                  selectedExtras[
                                                                                      rule
                                                                                          .id
                                                                                  ] ||
                                                                                  []
                                                                              ).includes(
                                                                                  extra.id
                                                                              )
                                                                                  ? "border-blue-500 bg-blue-50"
                                                                                  : "border-gray-300"
                                                                          }`,
                                                                      },
                                                                      React.createElement(
                                                                          "input",
                                                                          {
                                                                              type: "checkbox",
                                                                              checked:
                                                                                  (
                                                                                      selectedExtras[
                                                                                          rule
                                                                                              .id
                                                                                      ] ||
                                                                                      []
                                                                                  ).includes(
                                                                                      extra.id
                                                                                  ),
                                                                              onChange:
                                                                                  (
                                                                                      e
                                                                                  ) =>
                                                                                      handleExtraChange(
                                                                                          rule.id,
                                                                                          extra.id,
                                                                                          e
                                                                                              .target
                                                                                              .checked
                                                                                      ),
                                                                              disabled:
                                                                                  !canSelectExtra(
                                                                                      rule.id,
                                                                                      extra.id
                                                                                  ),
                                                                              className:
                                                                                  "rounded",
                                                                          }
                                                                      ),
                                                                      React.createElement(
                                                                          "span",
                                                                          {
                                                                              className:
                                                                                  "flex-grow",
                                                                          },
                                                                          extra.name
                                                                      ),
                                                                      React.createElement(
                                                                          "span",
                                                                          {
                                                                              className:
                                                                                  "text-green-600",
                                                                          },
                                                                          extra
                                                                              .prices[
                                                                              selectedSize
                                                                                  .id
                                                                          ]
                                                                              ? `+$${
                                                                                    extra
                                                                                        .prices[
                                                                                        selectedSize
                                                                                            .id
                                                                                    ]
                                                                                }`
                                                                              : "Free"
                                                                      )
                                                                  )
                                                          )
                                                      )
                                            )
                                        )
                                    ),
                                React.createElement(
                                    "div",
                                    { className: "space-y-2" },
                                    React.createElement(
                                        "h3",
                                        { className: "text-lg font-semibold" },
                                        "Quantity"
                                    ),
                                    React.createElement(
                                        "div",
                                        {
                                            className:
                                                "flex items-center space-x-4",
                                        },
                                        React.createElement(
                                            "button",
                                            {
                                                onClick: () =>
                                                    handleQuantityChange(
                                                        quantity - 1
                                                    ),
                                                className:
                                                    "w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center hover:bg-gray-300",
                                            },
                                            "-"
                                        ),
                                        React.createElement(
                                            "span",
                                            {
                                                className:
                                                    "text-lg font-medium",
                                            },
                                            quantity
                                        ),
                                        React.createElement(
                                            "button",
                                            {
                                                onClick: () =>
                                                    handleQuantityChange(
                                                        quantity + 1
                                                    ),
                                                className:
                                                    "w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center hover:bg-gray-300",
                                            },
                                            "+"
                                        )
                                    )
                                ),
                                React.createElement(
                                    "div",
                                    {
                                        className:
                                            "text-xl font-bold text-green-600 border-t pt-4",
                                    },
                                    `Total: $${totalPrice.toFixed(2)}`
                                ),
                                React.createElement(
                                    "div",
                                    { className: "flex space-x-3 pt-4" },
                                    React.createElement(
                                        "button",
                                        {
                                            onClick: closeModal,
                                            className:
                                                "flex-1 bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400 transition-colors",
                                        },
                                        "Cancel"
                                    ),
                                    React.createElement(
                                        "button",
                                        {
                                            onClick: handleSubmit,
                                            disabled: !selectedSize,
                                            className: `flex-1 px-4 py-2 rounded transition-colors ${
                                                selectedSize
                                                    ? "bg-green-500 text-white hover:bg-green-600"
                                                    : "bg-gray-300 text-gray-500 cursor-not-allowed"
                                            }`,
                                        },
                                        "Add to Cart"
                                    )
                                )
                            )
                    )
                )
        ),
        showSuccessModal &&
            React.createElement(
                "div",
                {
                    className:
                        "fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4",
                    onClick: () => setShowSuccessModal(false),
                },
                React.createElement(
                    "div",
                    {
                        className:
                            "bg-white rounded-lg p-8 max-w-md w-full text-center",
                        onClick: (e) => e.stopPropagation(),
                    },
                    React.createElement(
                        "div",
                        { className: "mb-4" },
                        React.createElement(
                            "div",
                            {
                                className:
                                    "w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4",
                            },
                            React.createElement(
                                "svg",
                                {
                                    className: "w-8 h-8 text-green-600",
                                    fill: "none",
                                    stroke: "currentColor",
                                    viewBox: "0 0 24 24",
                                },
                                React.createElement("path", {
                                    strokeLinecap: "round",
                                    strokeLinejoin: "round",
                                    strokeWidth: "2",
                                    d: "M5 13l4 4L19 7",
                                })
                            )
                        )
                    ),
                    React.createElement(
                        "h3",
                        { className: "text-xl font-bold text-gray-900 mb-2" },
                        "Order Submitted Successfully!"
                    ),
                    React.createElement(
                        "p",
                        { className: "text-gray-600 mb-6" },
                        "Your order has been added to the cart. Thank you for your purchase!"
                    ),
                    React.createElement(
                        "button",
                        {
                            onClick: () => setShowSuccessModal(false),
                            className:
                                "w-full bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition-colors",
                        },
                        "Continue Shopping"
                    )
                )
            )
    );
}

export default App;
