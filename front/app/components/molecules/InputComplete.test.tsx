import { render } from "@testing-library/react";
import { describe, expect, it } from "vitest";
import InputComplete from "./InputComplete";

describe("InputComplete", () => {
  it("should use the id prop as label for and input id", () => {
    // Arrange
    const testIdLabel = "test-value";
    const { container } = render(
      <InputComplete id={testIdLabel} label="Test" />,
    );

    // Assert
    expect(container.querySelector("label")).toHaveAttribute(
      "for",
      testIdLabel,
    );
    expect(container.querySelector("input")).toHaveAttribute("id", testIdLabel);
  });

  it.each([
    {
      variant: undefined,
      expectedClassName: "",
      expectedInputClassName: "w-64",
    },
    {
      variant: "md" as const,
      expectedClassName: "",
      expectedInputClassName: "w-64",
    },
    {
      variant: "full" as const,
      expectedClassName: "w-full",
      expectedInputClassName: "w-full",
    },
  ])(
    "should have $expectedClassName className and input child should have $expectedInputClassName className when variant is $variant",
    ({ variant, expectedClassName, expectedInputClassName }) => {
      // Arrange
      const { container } = render(
        <InputComplete id="test-value" label="Label Test" variant={variant} />,
      );

      // Assert
      if (expectedClassName) {
        expect(container.firstChild).toHaveClass(expectedClassName);
      }

      expect(container.querySelector("input")).toHaveClass(
        expectedInputClassName,
      );
    },
  );
});
