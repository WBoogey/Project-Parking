import { render, screen } from "@testing-library/react";
import { describe, expect, it, vi } from "vitest";
import SeePassword from "./SeePassword";
import userEvent from "@testing-library/user-event";

describe("SeePassword", () => {
  it.each([
    { currentState: false, expectedLabel: "Afficher le mot de passe" },
    { currentState: true, expectedLabel: "Masquer le mot de passe" },
  ])(
    "should have label $expectedLabel when visible is $currentState",
    ({ currentState, expectedLabel }) => {
      // Arrange
      const setVisible = vi.fn();
      const { container } = render(
        <SeePassword visible={currentState} setVisible={setVisible} />,
      );

      // Assert
      expect(container).toBeInTheDocument();
      expect(screen.getByLabelText(expectedLabel));
    },
  );

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
