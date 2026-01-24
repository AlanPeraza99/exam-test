const { createElement, render, useState, useEffect, useMemo } = wp.element;
const el = createElement;

/* --------------------------------------------------
 * Products slider
 * --------------------------------------------------*/
function ProductsSlider() {
  const [items, setItems] = useState([]);
  const [idx, setIdx] = useState(0);
  const [loading, setLoading] = useState(true);

  const total = items.length;
  const goPrev = () => setIdx((v) => (total ? (v - 1 + total) % total : 0));
  const goNext = () => setIdx((v) => (total ? (v + 1) % total : 0));

  useEffect(() => {
    let alive = true;

    async function load() {
      try {
        setLoading(true);
        const url = `${WP_DATA.restUrl}wp/v2/product?per_page=10&_embed=1`;
        const res = await fetch(url);
        const data = await res.json();

        if (!alive) return;
        setItems(Array.isArray(data) ? data : []);
        setIdx(0);
      } catch {
        if (!alive) return;
        setItems([]);
      } finally {
        if (!alive) return;
        setLoading(false);
      }
    }

    load();
    return () => {
      alive = false;
    };
  }, []);

  if (loading) {
    return el(
      "div",
      { className: "p-6 bg-white rounded-xl shadow" },
      el("p", { className: "text-tertiary" }, "Loading products...")
    );
  }

  if (!total) {
    return el(
      "div",
      { className: "p-6 bg-white rounded-xl shadow" },
      el("p", { className: "text-tertiary" }, "No products found.")
    );
  }

  const current = items[idx];
  const titleHTML = current?.title?.rendered || "Product";
  const excerptHTML = current?.excerpt?.rendered || "";
  const link = current?.link || "#";
  const featured = current?._embedded?.["wp:featuredmedia"]?.[0]?.source_url;

  return el(
    "section",
    { className: "bg-white rounded-xl shadow p-6" },

    el(
      "div",
      { className: "flex items-center justify-between mb-4" },
      el("h2", { className: "text-2xl font-bold text-secondary" }, "Products"),
      el("span", { className: "text-sm text-tertiary" }, `${idx + 1} / ${total}`)
    ),

    el(
      "div",
      { className: "grid md:grid-cols-2 gap-6 items-center" },
      featured
        ? el("img", {
            src: featured,
            className: "w-full h-64 object-cover rounded-xl",
          })
        : el("div", { className: "w-full h-64 bg-gray-100 rounded-xl" }),

      el(
        "div",
        null,
        el("h3", {
          className: "text-xl font-semibold text-secondary",
          dangerouslySetInnerHTML: { __html: titleHTML },
        }),
        el("div", {
          className: "mt-3 text-gray-600",
          dangerouslySetInnerHTML: { __html: excerptHTML },
        }),
        el(
          "a",
          {
            href: link,
            className: "inline-block mt-4 px-4 py-2 rounded bg-primary text-white",
          },
          "View product"
        )
      )
    ),

    el(
      "div",
      { className: "mt-6 flex justify-between" },
      el(
        "button",
        { onClick: goPrev, className: "px-4 py-2 bg-gray-100 rounded" },
        "Prev"
      ),
      el(
        "button",
        { onClick: goNext, className: "px-4 py-2 bg-gray-100 rounded" },
        "Next"
      )
    )
  );
}

/* --------------------------------------------------
 * Inquiry Modal (FORM)
 * --------------------------------------------------*/
function InquiryModal() {
  const [open, setOpen] = useState(false);
  const [sent, setSent] = useState(false);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);

  const [form, setForm] = useState({
    name: "",
    email: "",
    phone: "",
    topic: "Products",
    message: "",
    consent: false,
  });

  const canSubmit = useMemo(() => {
    return (
      form.name.trim() &&
      form.email.trim() &&
      form.message.trim() &&
      form.consent &&
      !loading
    );
  }, [form, loading]);

  const update = (k, v) => setForm((p) => ({ ...p, [k]: v }));

  const close = () => {
    setOpen(false);
    setSent(false);
    setError(null);
  };

  const submit = async (e) => {
    e.preventDefault();
    setError(null);
    setLoading(true);

    try {
      const res = await fetch(`${WP_DATA.restUrl}velovita/v1/inquiries`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-WP-Nonce": WP_DATA.nonce,
        },
        body: JSON.stringify(form),
      });

      const json = await res.json();
      if (!res.ok || !json.ok) throw new Error(json.message || "Submit failed");

      setSent(true);
      setForm({
        name: "",
        email: "",
        phone: "",
        topic: "Products",
        message: "",
        consent: false,
      });
    } catch (err) {
      setError(err.message);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    if (!open) return;
    const prev = document.body.style.overflow;
    document.body.style.overflow = "hidden";

    const esc = (e) => e.key === "Escape" && close();
    window.addEventListener("keydown", esc);
    return () => {
      document.body.style.overflow = prev;
      window.removeEventListener("keydown", esc);
    };
  }, [open]);

  return el(
    "div",
    null,

    el(
      "button",
      {
        className:
          "px-5 py-3 rounded-full bg-primary text-white font-semibold shadow hover:opacity-90",
        onClick: () => setOpen(true),
      },
      "Contact us"
    ),

    open &&
      el(
        "div",
        {
          className: "fixed inset-0 bg-black/50 flex items-center justify-center p-4 z-50",
          onClick: close,
        },
        el(
          "div",
          {
            className: "bg-white rounded-xl w-full max-w-xl p-6",
            onClick: (e) => e.stopPropagation(),
          },

          el("h3", { className: "text-xl font-bold text-secondary mb-4" },
            sent ? "Message received" : "Let’s talk"
          ),

          sent
            ? el("p", { className: "text-tertiary" }, "Thanks! We’ll contact you soon.")
            : el(
                "form",
                { onSubmit: submit, className: "flex flex-col gap-3" },

                el("input", {
                  placeholder: "Name *",
                  value: form.name,
                  onChange: (e) => update("name", e.target.value),
                  className: "border rounded px-3 py-2",
                }),
                el("input", {
                  placeholder: "Email *",
                  value: form.email,
                  onChange: (e) => update("email", e.target.value),
                  className: "border rounded px-3 py-2",
                }),
                el("textarea", {
                  placeholder: "Message *",
                  value: form.message,
                  onChange: (e) => update("message", e.target.value),
                  className: "border rounded px-3 py-2",
                }),

                el(
                  "label",
                  { className: "text-sm flex gap-2 items-start" },
                  el("input", {
                    type: "checkbox",
                    checked: form.consent,
                    onChange: (e) => update("consent", e.target.checked),
                  }),
                  "I accept the privacy policy"
                ),

                error && el("p", { className: "text-red-600 text-sm" }, error),

                el(
                  "button",
                  {
                    type: "submit",
                    disabled: !canSubmit,
                    className:
                      "mt-2 px-4 py-2 rounded bg-primary text-white disabled:opacity-50",
                  },
                  loading ? "Sending..." : "Send"
                )
              )
        )
      )
  );
}

/* --------------------------------------------------
 * Mount components
 * --------------------------------------------------*/
document.addEventListener("DOMContentLoaded", () => {

  const sliderEl = document.getElementById("react-products-slider");
  if (sliderEl) render(el(ProductsSlider), sliderEl);

  const inquiryEl = document.getElementById("react-inquiry-modal");
  if (inquiryEl) render(el(InquiryModal), inquiryEl);
});
