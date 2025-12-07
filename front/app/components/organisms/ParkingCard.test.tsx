import { render, screen } from "@testing-library/react";
import { describe, expect, it, vi } from "vitest";
import ParkingCard from "./ParkingCard";
import userEvent from "@testing-library/user-event";

const mockProps = {
  name: "Parking Central",
  totalSpots: 100,
  onEdit: vi.fn(),
};

describe("ParkingCard", () => {
  it("should render parking name", () => {
    // Arrange
    render(<ParkingCard {...mockProps} />);

    // Assert
    expect(screen.getByText("Parking Central")).toBeInTheDocument();
  });

  it("should call onEdit when edit button is clicked", async () => {
    // Arrange
    const handleEdit = vi.fn();
    render(<ParkingCard {...mockProps} onEdit={handleEdit} />);
    const user = userEvent.setup();

    // Act
    const editButton = screen.getByText("Editer");
    await user.click(editButton);

    // Assert
    expect(handleEdit).toHaveBeenCalled();
  });

  it.each([
    { availableSpots: 0, expectedText: "Complet" },
    { availableSpots: 1, expectedText: "1 place disponible" },
    { availableSpots: 5, expectedText: "5 places disponibles" },
    { availableSpots: undefined, expectedText: null },
  ])(
    "should display $expectedText for $availableSpots available spots",
    ({ availableSpots, expectedText }) => {
      // Arrange
      render(<ParkingCard {...mockProps} availableSpots={availableSpots} />);

      // Assert
      if (expectedText) {
        expect(screen.getByText(expectedText)).toBeInTheDocument();
      } else {
        expect(screen.queryByTestId("spots-status")).not.toBeInTheDocument();
      }
    },
  );
});
