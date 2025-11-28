import { render, screen } from "@testing-library/react";
import { describe, expect, it } from "vitest";
import SpotsStatus from "./SpotsStatus";

describe("SpotsStatus", () => {
  it.each([
    {
      capacity: 0,
      expectedText: "Complet",
      expectedClassName: "text-no-availability",
    },
    {
      capacity: 1,
      expectedText: "1 place disponible",
      expectedClassName: "text-low-availability",
    },
    {
      capacity: 5,
      expectedText: "5 places disponibles",
      expectedClassName: "text-low-availability",
    },
  ])(
    "should render capacity of $capacity as $expectedText",
    ({ capacity, expectedText, expectedClassName }) => {
      // Arrange
      render(<SpotsStatus capacity={capacity} />);
      const spotsStatus = screen.getByText(expectedText);

      // Assert
      expect(spotsStatus).toBeInTheDocument();
      expect(spotsStatus).toHaveClass(expectedClassName);
    },
  );

  it.each([{ capacity: -1 }, { capacity: -10 }])(
    "should render capacity of $capacity as 'Complet'",
    ({ capacity }) => {
      // Arrange
      render(<SpotsStatus capacity={capacity} />);

      // Assert
      expect(screen.getByText("Complet")).toBeInTheDocument();
    },
  );
});
