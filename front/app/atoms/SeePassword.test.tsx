import { render } from "@testing-library/react";
import { describe, expect, it, vi } from "vitest";
import SeePassword from "./SeePassword";
import userEvent from "@testing-library/user-event";

describe("SeePassword", () => {
  it("should render with default visible state / visible state is false", () => {
    // Arrange
    const setVisible = vi.fn();
    const { container } = render(
      <SeePassword visible={false} setVisible={setVisible} />,
    );

    // Assert
    expect(container).toBeInTheDocument();
    expect(container.querySelector("svg")).toHaveClass("lucide-eye-off");
  });

  it("should render with opened Eye icon when visible is true", () => {
    // Arrange
    const setVisible = vi.fn();
    const { container } = render(
      <SeePassword visible setVisible={setVisible} />,
    );

    // Assert
    expect(container.querySelector("svg")).toHaveClass("lucide-eye");
  });

  it.each([
    { currentState: true, expected: false },
    { currentState: false, expected: true },
  ])(
    "should call setVisible with $expected when clicked while visible is $currentState",
    async ({ currentState, expected }) => {
      // Arrange
      const setVisible = vi.fn();
      const { container } = render(
        <SeePassword visible={currentState} setVisible={setVisible} />,
      );

      // Act
      await userEvent.click(container.querySelector("button") as Element);

      // Assert
      expect(setVisible).toHaveBeenCalledTimes(1);
      expect(setVisible).toHaveBeenCalledWith(expected);
    },
  );
});
