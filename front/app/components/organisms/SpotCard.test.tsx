import { render, screen } from "@testing-library/react";
import { describe, expect, it, vi } from "vitest";
import SpotCard from "./SpotCard";
import userEvent from "@testing-library/user-event";

const mockProps = {
  name: "Parking Central",
  address: "123 Rue de la Paix",
  price: 100,
  onClick: vi.fn(),
};

describe("SpotCard", () => {
  it("should render with available state", () => {
    render(<SpotCard {...mockProps} capacity={5} />);
    const card = screen.getByTestId("spot-card");

    expect(screen.getByText("Parking Central")).toBeInTheDocument();
    expect(screen.getByText("123 Rue de la Paix")).toBeInTheDocument();
    expect(card).toHaveClass("bg-primary");
  });

  it("should render with complete state when capacity is 0", () => {
    render(<SpotCard {...mockProps} capacity={0} />);
    const card = screen.getByTestId("spot-card");

    expect(card).not.toHaveClass("bg-primary");
  });

  it("should render with complete state when capacity is negative", () => {
    render(<SpotCard {...mockProps} capacity={-1} />);
    const card = screen.getByTestId("spot-card");

    expect(card).not.toHaveClass("bg-primary");
  });

  it("should display image when imageUrl is provided", () => {
    render(
      <SpotCard
        {...mockProps}
        capacity={5}
        imageUrl="https://example.com/image.jpg"
      />,
    );

    const image = screen.getByAltText("Parking Central");
    expect(image).toBeInTheDocument();
    expect(image).toHaveAttribute("src", "https://example.com/image.jpg");
  });

  it("should display placeholder when imageUrl is not provided", () => {
    const { container } = render(<SpotCard {...mockProps} capacity={5} />);
    const placeholder = container.querySelector(".bg-zinc-700");

    expect(placeholder).toBeInTheDocument();
  });

  it("should call onClick when clicked", async () => {
    const handleClick = vi.fn();
    render(<SpotCard {...mockProps} capacity={5} onClick={handleClick} />);
    const user = userEvent.setup();

    const card = screen.getByTestId("spot-card");
    await user.click(card);

    expect(handleClick).toHaveBeenCalled();
  });

  it.each([
    { capacity: 5, state: "available", shouldHaveBg: true },
    { capacity: 1, state: "available", shouldHaveBg: true },
    { capacity: 0, state: "complete", shouldHaveBg: false },
    { capacity: -1, state: "complete", shouldHaveBg: false },
  ] as const)(
    "should apply correct styles for $state state (capacity: $capacity)",
    ({ capacity, shouldHaveBg }) => {
      render(<SpotCard {...mockProps} capacity={capacity} />);
      const card = screen.getByTestId("spot-card");

      if (shouldHaveBg) {
        expect(card).toHaveClass("bg-primary");
      } else {
        expect(card).not.toHaveClass("bg-primary");
      }
    },
  );
});
