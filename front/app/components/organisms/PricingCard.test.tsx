import { render, screen } from "@testing-library/react";
import { describe, expect, it, vi } from "vitest";
import PricingCard from "./PricingCard";
import userEvent from "@testing-library/user-event";

const mockProps = {
  onClick: vi.fn(),
};

describe("PricingCard", () => {
  it.each([
    { variant: "hourly" as const, expectedText: "À l'heure" },
    { variant: "monthly" as const, expectedText: "Au mois" },
  ])(
    "should display $expectedText for $variant variant",
    ({ variant, expectedText }) => {
      // Arrange
      render(<PricingCard {...mockProps} variant={variant} />);

      // Assert
      expect(screen.getByText(expectedText)).toBeInTheDocument();
    },
  );

  it.each([
    { price: 50, shouldDisplay: true },
    { price: undefined, shouldDisplay: false },
  ])(
    "should display price correctly when price is $price",
    ({ price, shouldDisplay }) => {
      // Arrange
      render(<PricingCard {...mockProps} price={price} />);

      // Assert
      if (shouldDisplay) {
        expect(screen.getByText(`${price}€`)).toBeInTheDocument();
      } else {
        expect(screen.queryByText(/€/)).not.toBeInTheDocument();
      }
    },
  );

  it.each([
    {
      items: [
        { label: "1 heure", price: 2 },
        { label: "2 heures", price: 3.5 },
      ],
      shouldRenderList: true,
      description: "items provided",
    },
    { items: [], shouldRenderList: false, description: "empty array" },
    { items: undefined, shouldRenderList: false, description: "undefined" },
  ])(
    "should render items list correctly when items is $description",
    ({ items, shouldRenderList }) => {
      // Arrange
      render(<PricingCard {...mockProps} items={items} />);

      // Assert
      if (shouldRenderList) {
        expect(screen.getByRole("list")).toBeInTheDocument();
        items?.forEach((item) => {
          expect(screen.getByText(item.label)).toBeInTheDocument();
        });
      } else {
        expect(screen.queryByRole("list")).not.toBeInTheDocument();
      }
    },
  );

  it("should call onClick when card is clicked", async () => {
    // Arrange
    const handleClick = vi.fn();
    render(<PricingCard {...mockProps} onClick={handleClick} />);
    const user = userEvent.setup();

    // Act
    await user.click(screen.getByRole("button"));

    // Assert
    expect(handleClick).toHaveBeenCalled();
  });
});
