import { render, screen } from "@testing-library/react";
import { describe, expect, it, vi } from "vitest";
import SeatProCard from "./SeatProCard";
import userEvent from "@testing-library/user-event";

const mockPropsOccupied = {
  name: "A1",
  status: "occupied" as const,
  occupiedBy: "Jean Dupont",
  reservationType: "daily" as const,
  timeRange: { start: "09:00", end: "18:00" },
};

const mockPropsFree = {
  name: "B2",
  status: "free" as const,
};

describe("SeatProCard", () => {
  describe("Rendering - Status", () => {
    it.each([
      {
        props: mockPropsFree,
        expectedText: "Libre",
        description: "free status",
      },
      {
        props: mockPropsOccupied,
        expectedText: "Occupée par",
        description: "occupied status",
      },
    ])("should display $description correctly", ({ props, expectedText }) => {
      // Arrange
      render(<SeatProCard {...props} />);

      // Assert
      expect(
        screen.getByText(expectedText, { exact: false }),
      ).toBeInTheDocument();
    });

    it("should display seat name", () => {
      // Arrange
      render(<SeatProCard {...mockPropsFree} />);

      // Assert
      expect(screen.getByText("B2")).toBeInTheDocument();
    });

    it("should display client name when occupied", () => {
      // Arrange
      render(<SeatProCard {...mockPropsOccupied} />);

      // Assert
      expect(screen.getByText("Jean Dupont")).toBeInTheDocument();
    });
  });

  describe("Rendering - Reservation Type", () => {
    it.each([
      {
        reservationType: "daily" as const,
        expectedLabel: "Journée :",
      },
      {
        reservationType: "monthly" as const,
        expectedLabel: "Mensuel :",
      },
    ])(
      "should display $reservationType reservation label",
      ({ reservationType, expectedLabel }) => {
        // Arrange
        render(
          <SeatProCard
            {...mockPropsOccupied}
            reservationType={reservationType}
          />,
        );

        // Assert
        expect(screen.getByText(expectedLabel)).toBeInTheDocument();
      },
    );

    it("should display time range", () => {
      // Arrange
      render(<SeatProCard {...mockPropsOccupied} />);

      // Assert
      expect(screen.getByText("09:00 - 18:00")).toBeInTheDocument();
    });
  });

  describe("Rendering - Limit Reached", () => {
    it("should display limit reached message when limitReached is true", () => {
      // Arrange
      render(<SeatProCard {...mockPropsOccupied} limitReached />);

      // Assert
      expect(screen.getByText("Limite atteinte")).toBeInTheDocument();
    });

    it("should not display limit reached message when limitReached is false", () => {
      // Arrange
      render(<SeatProCard {...mockPropsOccupied} limitReached={false} />);

      // Assert
      expect(screen.queryByText("Limite atteinte")).not.toBeInTheDocument();
    });
  });

  describe("Background Color", () => {
    it.each([
      {
        status: "free" as const,
        limitReached: false,
        expectedClass: "bg-available/60",
        description: "free without limit",
      },
      {
        status: "free" as const,
        limitReached: true,
        expectedClass: "bg-no-availability/60",
        description: "free with limit",
      },
      {
        status: "occupied" as const,
        limitReached: false,
        expectedClass: "bg-low-availability/60",
        description: "occupied without limit",
      },
      {
        status: "occupied" as const,
        limitReached: true,
        expectedClass: "bg-no-availability/60",
        description: "occupied with limit",
      },
    ])(
      "should have correct background for $description",
      ({ status, limitReached, expectedClass }) => {
        // Arrange
        render(
          <SeatProCard
            name="Test"
            status={status}
            limitReached={limitReached}
          />,
        );

        // Assert
        expect(screen.getByRole("button")).toHaveClass(expectedClass);
      },
    );
  });

  describe("Callback", () => {
    it("should call onClick when clicked", async () => {
      // Arrange
      const handleClick = vi.fn();
      render(<SeatProCard {...mockPropsFree} onClick={handleClick} />);
      const user = userEvent.setup();

      // Act
      await user.click(screen.getByRole("button"));

      // Assert
      expect(handleClick).toHaveBeenCalledOnce();
    });
  });
});
