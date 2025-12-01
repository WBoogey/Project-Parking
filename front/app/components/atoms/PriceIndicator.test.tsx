import { render } from "@testing-library/react";
import { describe, expect, it } from "vitest";
import PriceIndicator from "./PriceIndicator";

describe("PriceIndicator", () => {
  it("should render with default values", () => {
    // Assert
    const { container } = render(
      <PriceIndicator price={100} data-testid="component" />,
    );

    // Assert
    expect(container.firstChild).toHaveClass(
      "flex-col",
      "justify-between",
      "h-full",
    );
    expect(container).toHaveTextContent("100EUR");
    expect(container).toHaveTextContent("Par mois");
  });

  it.each([
    {
      frequency: "weekly" as const,
      expected: "Par semaine",
    },
    {
      frequency: "monthly" as const,
      expected: "Par mois",
    },
    {
      frequency: "yearly" as const,
      expected: "Par an",
    },
  ])(
    "should render $frequency frequency as $expected",
    ({ frequency, expected }) => {
      // Arrange
      const { container } = render(
        <PriceIndicator price={100} frequency={frequency} />,
      );

      // Assert
      expect(container).toHaveTextContent(expected);
    },
  );

  it("should render inline variant differently", () => {
    // Arrange
    const { container } = render(
      <PriceIndicator price={100} variant="inline" />,
    );

    // Assert
    expect(container.firstChild).toHaveClass("gap-1");
    expect(container.firstChild).toHaveTextContent("/mois");
  });
});
