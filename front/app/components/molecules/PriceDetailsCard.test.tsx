import { render, screen, within } from "@testing-library/react";
import { describe, expect, it } from "vitest";
import PriceDetailsCard from "./PriceDetailsCard";
import type { SpotVariant } from "@/types/SpotsTypes";

describe("PriceDetailsCard", () => {
  it.each([
    {
      variant: "car" as Partial<SpotVariant>,
      text: "Place de voiture",
      testId: "car-icon",
    },
    {
      variant: "motorcycle" as Partial<SpotVariant>,
      text: "Place de moto",
      testId: "motorcycle-icon",
    },
  ])(
    "should render a $testId with $text content for $variant variant",
    ({ variant, text, testId }) => {
      // Arrange
      const { container } = render(
        <PriceDetailsCard variant={variant} price={100} />,
      );

      // Assert
      expect(container).toBeInTheDocument();
      expect(screen.getByTestId(testId)).toBeInTheDocument();
      expect(
        within(screen.getByTestId("text-label")).getByText(text),
      ).toBeInTheDocument();
    },
  );

  it.each([
    { capacity: 2, expectedText: "2 places disponibles" },
    { capacity: 0, expectedText: "Complet" },
    {
      capacity: null,
      expectedText: null,
    },
  ])(
    "should render $expectedText with $capacity capacity",
    ({ capacity, expectedText }) => {
      render(
        <PriceDetailsCard
          variant="car"
          price={100}
          capacity={capacity ?? undefined}
        />,
      );

      const possibleStatusText = [
        "Complet",
        "place disponible",
        "places disponibles",
      ];

      if (expectedText) {
        expect(screen.getByText(expectedText)).toBeInTheDocument();
      } else {
        possibleStatusText.forEach((text) => {
          expect(
            screen.queryByText(new RegExp(text, "i")),
          ).not.toBeInTheDocument();
        });
      }
    },
  );
});
