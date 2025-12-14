interface PricingDetailsItemProps {
  label: string;
  price: number;
}

const PricingDetailsItem = ({ label, price }: PricingDetailsItemProps) => {
  const formattedPrice = new Intl.NumberFormat("fr-FR", {
    style: "currency",
    currency: "EUR",
  }).format(price);

  return (
    <li className="flex items-center justify-between text-secondary font-medium">
      <span>{label}</span>
      <span>{formattedPrice}</span>
    </li>
  );
};

export default PricingDetailsItem;
