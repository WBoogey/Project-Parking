import { fireEvent, render, screen } from "@testing-library/react";
import { describe, expect, it, vi } from "vitest";
import Button from "./Button";

describe("Button", () => {
  it("should render children", () => {
    // Arrange
    render(<Button onClick={() => {}}>Click me</Button>);

    // Assert
    expect(screen.getByText("Click me"));
  });

  it("should call onClick callback when clicked", () => {
    // Arrange
    const handleClick = vi.fn();
    render(<Button onClick={handleClick}>Click me</Button>);

    // Act
    fireEvent.click(screen.getByText("Click me"));

    // Assert
    expect(handleClick).toBeCalled();
  });

  it("should apply correct size", () => {
    // Arrange
    render(
      <Button size="lg" onClick={() => {}}>
        Click me
      </Button>,
    );

    // Assert
    expect(screen.getByText("Click me")).toHaveClass("w-120");
  });
});
